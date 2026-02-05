package models

import (
	"database/sql"
	"event-backend/internal/database"
	"time"
)

type Banner struct {
	ID        int       `json:"id"`
	Slug      string    `json:"slug"`
	Title     string    `json:"title"`
	ImagePath string    `json:"image_path"`
	LinkURL   string    `json:"link_url"`
	IsActive  bool      `json:"is_active"`
	CreatedAt time.Time `json:"created_at"`
	UpdatedAt time.Time `json:"updated_at"`
}

func GetAllBanners() ([]Banner, error) {
	rows, err := database.DB.Query(`SELECT id, slug, title, image_path, link_url, is_active, created_at FROM banners ORDER BY created_at DESC`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var banners []Banner
	for rows.Next() {
		var b Banner
		var title, linkURL sql.NullString
		err := rows.Scan(&b.ID, &b.Slug, &title, &b.ImagePath, &linkURL, &b.IsActive, &b.CreatedAt)
		if err != nil {
			return nil, err
		}
		b.Title = title.String
		b.LinkURL = linkURL.String
		banners = append(banners, b)
	}
	return banners, nil
}

func GetActiveBanners() ([]Banner, error) {
	rows, err := database.DB.Query(`SELECT id, slug, title, image_path, link_url, is_active, created_at FROM banners WHERE is_active = true ORDER BY created_at DESC`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var banners []Banner
	for rows.Next() {
		var b Banner
		var title, linkURL sql.NullString
		err := rows.Scan(&b.ID, &b.Slug, &title, &b.ImagePath, &linkURL, &b.IsActive, &b.CreatedAt)
		if err != nil {
			return nil, err
		}
		b.Title = title.String
		b.LinkURL = linkURL.String
		banners = append(banners, b)
	}
	return banners, nil
}

func CreateBanner(b *Banner) error {
	query := `INSERT INTO banners (slug, title, image_path, link_url, is_active, created_at, updated_at) VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING id`
	return database.DB.QueryRow(query,
		b.Slug,
		sql.NullString{String: b.Title, Valid: b.Title != ""},
		b.ImagePath,
		sql.NullString{String: b.LinkURL, Valid: b.LinkURL != ""},
		b.IsActive, time.Now(), time.Now(),
	).Scan(&b.ID)
}

func UpdateBanner(b *Banner) error {
	query := `UPDATE banners SET slug=$1, title=$2, image_path=$3, link_url=$4, is_active=$5, updated_at=$6 WHERE id=$7`
	_, err := database.DB.Exec(query,
		b.Slug,
		sql.NullString{String: b.Title, Valid: b.Title != ""},
		b.ImagePath,
		sql.NullString{String: b.LinkURL, Valid: b.LinkURL != ""},
		b.IsActive, time.Now(), b.ID,
	)
	return err
}

func DeleteBanner(id int) error {
	// Assuming soft deletes if schema has deleted_at, otherwise hard delete.
	// Migration showed deleted_at is NOT in schema (my bad? check migration).
	// Migration 2025_12_28_160444_create_banners_table.php:
	// $table->timestamps(); but NO softDeletes().
	// So I should use hard delete OR add softDeletes column.
	// For "consistency" let's check migration again.
	// Migration 2025_12_28_160444_create_banners_table.php does NOT show softDeletes in the dump I saw earlier.
	// So I will use HARD DELETE for now to match schema.
	_, err := database.DB.Exec("DELETE FROM banners WHERE id=$1", id)
	return err
}
