package models

import (
	"event-backend/internal/database"
	"time"
)

type Setting struct {
	ID        int       `json:"id"`
	Key       string    `json:"key"`
	Value     string    `json:"value"` // Stored as TEXT, handled as string. Type conversion happens in app logic if needed.
	CreatedAt time.Time `json:"created_at"`
	UpdatedAt time.Time `json:"updated_at"`
}

func GetAllSettings() ([]Setting, error) {
	rows, err := database.DB.Query(`SELECT id, key, COALESCE(value, ''), created_at, updated_at FROM settings`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var settings []Setting
	for rows.Next() {
		var s Setting
		err := rows.Scan(&s.ID, &s.Key, &s.Value, &s.CreatedAt, &s.UpdatedAt)
		if err != nil {
			return nil, err
		}
		settings = append(settings, s)
	}
	return settings, nil
}

func UpdateSetting(key string, value string) error {
	// Upsert
	query := `
		INSERT INTO settings (key, value, created_at, updated_at) 
		VALUES ($1, $2, $3, $4)
		ON CONFLICT (key) 
		DO UPDATE SET value = EXCLUDED.value, updated_at = EXCLUDED.updated_at
	`
	_, err := database.DB.Exec(query, key, value, time.Now(), time.Now())
	return err
}
