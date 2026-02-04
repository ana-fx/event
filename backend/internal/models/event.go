package models

import (
	"database/sql"
	"event-backend/internal/database"
	"time"
)

type Event struct {
	ID                     int            `json:"id"`
	Name                   string         `json:"name"`
	Slug                   string         `json:"slug"`
	Category               string         `json:"category"`
	Status                 string         `json:"status"`
	BannerPath             sql.NullString `json:"banner_path"`
	ThumbnailPath          sql.NullString `json:"thumbnail_path"`
	StartDate              time.Time      `json:"start_date"`
	EndDate                time.Time      `json:"end_date"`
	Description            string         `json:"description"`
	Terms                  sql.NullString `json:"terms"`
	Location               sql.NullString `json:"location"`
	Province               sql.NullString `json:"province"`
	City                   sql.NullString `json:"city"`
	Zip                    sql.NullString `json:"zip"`
	GoogleMapEmbed         sql.NullString `json:"google_map_embed"`
	SeoTitle               sql.NullString `json:"seo_title"`
	SeoDescription         sql.NullString `json:"seo_description"`
	OrganizerName          sql.NullString `json:"organizer_name"`
	OrganizerLogoPath      sql.NullString `json:"organizer_logo_path"`
	ResellerFeeType        string         `json:"reseller_fee_type"`
	ResellerFeeValue       float64        `json:"reseller_fee_value"`
	OrganizerFeeOnlineType string         `json:"organizer_fee_online_type"`
	OrganizerFeeOnline     float64        `json:"organizer_fee_online"`
	CreatedAt              time.Time      `json:"created_at"`
	UpdatedAt              time.Time      `json:"updated_at"`
}

func GetAllEvents() ([]Event, error) {
	rows, err := database.DB.Query(`
		SELECT id, name, slug, category, status, start_date, end_date, description, location, city, created_at 
		FROM events 
		WHERE deleted_at IS NULL 
		ORDER BY created_at DESC
	`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var events []Event
	for rows.Next() {
		var e Event
		// Scanning subset for list view
		err := rows.Scan(
			&e.ID, &e.Name, &e.Slug, &e.Category, &e.Status,
			&e.StartDate, &e.EndDate, &e.Description,
			&e.Location, &e.City, &e.CreatedAt,
		)
		if err != nil {
			return nil, err
		}
		events = append(events, e)
	}
	return events, nil
}

func CreateEvent(e *Event) error {
	query := `
		INSERT INTO events (
			name, slug, category, status, start_date, end_date, description, location, 
			reseller_fee_type, reseller_fee_value, 
			organizer_fee_online_type, organizer_fee_online,
			organizer_fee_reseller_type, organizer_fee_reseller,
			created_at, updated_at
		) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, 'fixed', 0, 'fixed', 0, 'fixed', 0, $9, $10)
		RETURNING id`

	err := database.DB.QueryRow(query,
		e.Name, e.Slug, e.Category, e.Status, e.StartDate, e.EndDate, e.Description, e.Location,
		time.Now(), time.Now(),
	).Scan(&e.ID)

	return err
}

func UpdateEvent(e *Event) error {
	query := `
		UPDATE events 
		SET name=$1, slug=$2, category=$3, status=$4, start_date=$5, end_date=$6, description=$7, location=$8, updated_at=$9
		WHERE id=$10`

	_, err := database.DB.Exec(query,
		e.Name, e.Slug, e.Category, e.Status, e.StartDate, e.EndDate, e.Description, e.Location,
		time.Now(), e.ID,
	)
	return err
}

func DeleteEvent(id int) error {
	// Soft delete
	query := `UPDATE events SET deleted_at=$1 WHERE id=$2`
	_, err := database.DB.Exec(query, time.Now(), id)
	return err
}

func GetEventByID(id int) (*Event, error) {
	query := `SELECT id, name, slug, category, status, start_date, end_date, description, location, created_at FROM events WHERE id=$1 AND deleted_at IS NULL`
	var e Event
	err := database.DB.QueryRow(query, id).Scan(
		&e.ID, &e.Name, &e.Slug, &e.Category, &e.Status,
		&e.StartDate, &e.EndDate, &e.Description,
		&e.Location, &e.CreatedAt,
	)
	if err != nil {
		return nil, err
	}
	return &e, nil
}

func GetPublishedEvents() ([]Event, error) {
	rows, err := database.DB.Query(`
		SELECT id, name, slug, category, status, start_date, end_date, description, location, city, thumbnail_path, created_at 
		FROM events 
		WHERE status = 'published' AND deleted_at IS NULL 
		ORDER BY created_at DESC
	`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var events []Event
	for rows.Next() {
		var e Event
		err := rows.Scan(
			&e.ID, &e.Name, &e.Slug, &e.Category, &e.Status,
			&e.StartDate, &e.EndDate, &e.Description,
			&e.Location, &e.City, &e.ThumbnailPath, &e.CreatedAt,
		)
		if err != nil {
			return nil, err
		}
		events = append(events, e)
	}
	return events, nil
}

func GetEventBySlug(slug string) (*Event, error) {
	query := `SELECT id, name, slug, category, status, start_date, end_date, description, location, city, banner_path, created_at FROM events WHERE slug=$1 AND deleted_at IS NULL`
	var e Event
	err := database.DB.QueryRow(query, slug).Scan(
		&e.ID, &e.Name, &e.Slug, &e.Category, &e.Status,
		&e.StartDate, &e.EndDate, &e.Description,
		&e.Location, &e.City, &e.BannerPath, &e.CreatedAt,
	)
	if err != nil {
		return nil, err
	}
	return &e, nil
}

func CountActiveEvents() (int, error) {
	var count int
	err := database.DB.QueryRow(`SELECT count(*) FROM events WHERE deleted_at IS NULL`).Scan(&count)
	return count, err
}

// Assignments
func AssignScanner(eventID, userID int) error {
	_, err := database.DB.Exec(`INSERT INTO event_scanner (event_id, user_id, created_at, updated_at) VALUES ($1, $2, $3, $4) ON CONFLICT DO NOTHING`, eventID, userID, time.Now(), time.Now())
	return err
}

func UnassignScanner(eventID, userID int) error {
	_, err := database.DB.Exec(`DELETE FROM event_scanner WHERE event_id=$1 AND user_id=$2`, eventID, userID)
	return err
}

func GetEventScanners(eventID int) ([]User, error) {
	rows, err := database.DB.Query(`
		SELECT u.id, u.name, u.email, u.is_active 
		FROM users u 
		JOIN event_scanner es ON u.id = es.user_id 
		WHERE es.event_id = $1
	`, eventID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var users []User
	for rows.Next() {
		var u User
		err := rows.Scan(&u.ID, &u.Name, &u.Email, &u.IsActive)
		if err != nil {
			return nil, err
		}
		users = append(users, u)
	}
	return users, nil
}

func AssignReseller(eventID, userID int, commissionType string, commissionValue float64) error {
	_, err := database.DB.Exec(`
		INSERT INTO event_reseller (event_id, user_id, commission_type, commission_value, organizer_fee, created_at, updated_at) 
		VALUES ($1, $2, $3, $4, 0, $5, $6) 
		ON CONFLICT DO NOTHING
	`, eventID, userID, commissionType, commissionValue, time.Now(), time.Now())
	return err
}

func UnassignReseller(eventID, userID int) error {
	_, err := database.DB.Exec(`DELETE FROM event_reseller WHERE event_id=$1 AND user_id=$2`, eventID, userID)
	return err
}

func GetEventResellers(eventID int) ([]struct {
	User
	CommissionType  string
	CommissionValue float64
}, error) {
	rows, err := database.DB.Query(`
		SELECT u.id, u.name, u.email, u.is_active, er.commission_type, er.commission_value
		FROM users u 
		JOIN event_reseller er ON u.id = er.user_id 
		WHERE er.event_id = $1
	`, eventID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var result []struct {
		User
		CommissionType  string
		CommissionValue float64
	}
	for rows.Next() {
		var item struct {
			User
			CommissionType  string
			CommissionValue float64
		}
		err := rows.Scan(&item.ID, &item.Name, &item.Email, &item.IsActive, &item.CommissionType, &item.CommissionValue)
		if err != nil {
			return nil, err
		}
		result = append(result, item)
	}
	return result, nil
}
