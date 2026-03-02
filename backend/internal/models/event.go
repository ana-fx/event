package models

import (
	"event-backend/internal/database"
	"fmt"
	"time"
)

type Event struct {
	ID                       int       `json:"id"`
	Name                     string    `json:"name"`
	Slug                     string    `json:"slug"`
	Category                 string    `json:"category"`
	Status                   string    `json:"status"`
	BannerPath               *string   `json:"banner_path"`
	ThumbnailPath            *string   `json:"thumbnail_path"`
	StartDate                time.Time `json:"start_date"`
	EndDate                  time.Time `json:"end_date"`
	Description              string    `json:"description"`
	Terms                    *string   `json:"terms"`
	Location                 *string   `json:"location"`
	Province                 *string   `json:"province"`
	City                     *string   `json:"city"`
	Zip                      *string   `json:"zip"`
	GoogleMapEmbed           *string   `json:"google_map_embed"`
	SeoTitle                 *string   `json:"seo_title"`
	SeoDescription           *string   `json:"seo_description"`
	OrganizerName            *string   `json:"organizer_name"`
	OrganizerLogoPath        *string   `json:"organizer_logo_path"`
	OrganizerID              *int      `json:"organizer_id"`
	ResellerFeeType          string    `json:"reseller_fee_type"`
	ResellerFeeValue         float64   `json:"reseller_fee_value"`
	OrganizerFeeOnlineType   string    `json:"organizer_fee_online_type"`
	OrganizerFeeOnline       float64   `json:"organizer_fee_online"`
	OrganizerFeeResellerType string    `json:"organizer_fee_reseller_type"`
	OrganizerFeeReseller     float64   `json:"organizer_fee_reseller"`
	OrganizerTax             float64   `json:"organizer_tax"`
	OrganizerTaxType         string    `json:"organizer_tax_type"`
	AdminFee                 float64   `json:"admin_fee"`
	AdminFeeType             string    `json:"admin_fee_type"`
	PPN                      float64   `json:"ppn"`
	PPNType                  string    `json:"ppn_type"`
	PgFee                    float64   `json:"pg_fee"`
	PgFeeType                string    `json:"pg_fee_type"`
	PgFeeBank                float64   `json:"pg_fee_bank"`
	PgFeeQris                float64   `json:"pg_fee_qris"`
	MinPrice                 float64   `json:"min_price"`
	YoutubeLink              *string   `json:"youtube_link"`
	CreatedAt                time.Time `json:"created_at"`
	UpdatedAt                time.Time `json:"updated_at"`
}

func GetAllEvents(organizerID *int) ([]Event, error) {
	query := `
		SELECT e.id, e.name, e.slug, e.category, e.status, e.banner_path, e.thumbnail_path, e.start_date, e.end_date, e.description, e.location, e.city, 
		       COALESCE(u.organizer_name, e.organizer_name) as organizer_name, 
		       e.youtube_link, e.organizer_tax, e.organizer_tax_type, e.admin_fee, e.admin_fee_type, e.ppn, e.ppn_type, e.created_at,
		       e.organizer_id
		FROM events e
		LEFT JOIN users u ON e.organizer_id = u.id
		WHERE e.deleted_at IS NULL`

	var args []interface{}
	if organizerID != nil {
		query += ` AND e.organizer_id = $1`
		args = append(args, *organizerID)
	}

	query += ` ORDER BY e.created_at DESC`

	rows, err := database.DB.Query(query, args...)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	events := []Event{}
	for rows.Next() {
		var e Event
		err := rows.Scan(
			&e.ID, &e.Name, &e.Slug, &e.Category, &e.Status,
			&e.BannerPath, &e.ThumbnailPath,
			&e.StartDate, &e.EndDate, &e.Description,
			&e.Location, &e.City, &e.OrganizerName,
			&e.YoutubeLink,
			&e.OrganizerTax, &e.OrganizerTaxType, &e.AdminFee, &e.AdminFeeType, &e.PPN, &e.PPNType,
			&e.CreatedAt,
			&e.OrganizerID,
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
			name, slug, category, status, start_date, end_date, description, terms, 
			location, province, city, zip, google_map_embed,
			seo_title, seo_description,
			organizer_name, banner_path, thumbnail_path, organizer_logo_path,
			youtube_link,
			organizer_tax, organizer_tax_type, admin_fee, admin_fee_type, ppn, ppn_type,
			reseller_fee_type, reseller_fee_value, 
			organizer_fee_online_type, organizer_fee_online,
			organizer_fee_reseller_type, organizer_fee_reseller,
			pg_fee, pg_fee_type, pg_fee_bank, pg_fee_qris,
			organizer_id,
			created_at, updated_at
		) VALUES (
			$1, $2, $3, $4, $5, $6, $7, $8, 
			$9, $10, $11, $12, $13,
			$14, $15,
			$16, $17, $18, $19,
			$20,
			$21, $22, $23, $24, $25, $26,
			$27, $28, $29, $30, $31, $32,
			$33, $34, $35, $36,
			$37,
			$38, $39
		)
		RETURNING id`

	err := database.DB.QueryRow(query,
		e.Name, e.Slug, e.Category, e.Status, e.StartDate, e.EndDate, e.Description, e.Terms,
		e.Location, e.Province, e.City, e.Zip, e.GoogleMapEmbed,
		e.SeoTitle, e.SeoDescription,
		e.OrganizerName, e.BannerPath, e.ThumbnailPath, e.OrganizerLogoPath,
		e.YoutubeLink,
		e.OrganizerTax, e.OrganizerTaxType, e.AdminFee, e.AdminFeeType, e.PPN, e.PPNType,
		e.ResellerFeeType, e.ResellerFeeValue, e.OrganizerFeeOnlineType, e.OrganizerFeeOnline,
		e.OrganizerFeeResellerType, e.OrganizerFeeReseller,
		e.PgFee, e.PgFeeType, e.PgFeeBank, e.PgFeeQris,
		e.OrganizerID,
		time.Now(), time.Now(),
	).Scan(&e.ID)

	return err
}

func UpdateEvent(e *Event) error {
	query := `
		UPDATE events 
		SET name=$1, slug=$2, category=$3, status=$4, start_date=$5, end_date=$6, description=$7, terms=$8,
		    location=$9, province=$10, city=$11, zip=$12, google_map_embed=$13,
			seo_title=$14, seo_description=$15,
			organizer_name=$16, banner_path=$17, thumbnail_path=$18, organizer_logo_path=$19,
			youtube_link=$20,
			organizer_tax=$21, organizer_tax_type=$22, admin_fee=$23, admin_fee_type=$24, ppn=$25, ppn_type=$26,
			reseller_fee_type=$27, reseller_fee_value=$28, organizer_fee_online_type=$29, organizer_fee_online=$30,
			organizer_fee_reseller_type=$31, organizer_fee_reseller=$32,
			pg_fee=$33, pg_fee_type=$34, pg_fee_bank=$35, pg_fee_qris=$36,
			organizer_id=$37,
			updated_at=$38
		WHERE id=$39`

	_, err := database.DB.Exec(query,
		e.Name, e.Slug, e.Category, e.Status, e.StartDate, e.EndDate, e.Description, e.Terms,
		e.Location, e.Province, e.City, e.Zip, e.GoogleMapEmbed,
		e.SeoTitle, e.SeoDescription,
		e.OrganizerName, e.BannerPath, e.ThumbnailPath, e.OrganizerLogoPath,
		e.YoutubeLink,
		e.OrganizerTax, e.OrganizerTaxType, e.AdminFee, e.AdminFeeType, e.PPN, e.PPNType,
		e.ResellerFeeType, e.ResellerFeeValue, e.OrganizerFeeOnlineType, e.OrganizerFeeOnline,
		e.OrganizerFeeResellerType, e.OrganizerFeeReseller,
		e.PgFee, e.PgFeeType, e.PgFeeBank, e.PgFeeQris,
		e.OrganizerID,
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
	query := `
		SELECT 
			e.id, e.name, e.slug, e.category, e.status, e.start_date, e.end_date, e.description, e.terms,
			e.location, e.province, e.city, e.zip, e.google_map_embed,
			e.seo_title, e.seo_description,
			COALESCE(u.organizer_name, e.organizer_name) as organizer_name, 
			e.banner_path, e.thumbnail_path, 
			COALESCE(u.profile_photo_path, e.organizer_logo_path) as organizer_logo_path,
			e.youtube_link,
			e.organizer_tax, e.organizer_tax_type, e.admin_fee, e.admin_fee_type, e.ppn, e.ppn_type,
			e.reseller_fee_type, e.reseller_fee_value, e.organizer_fee_online_type, e.organizer_fee_online,
			e.organizer_fee_reseller_type, e.organizer_fee_reseller,
			e.pg_fee, e.pg_fee_type, e.pg_fee_bank, e.pg_fee_qris,
			e.created_at,
			e.organizer_id
		FROM events e
		LEFT JOIN users u ON e.organizer_id = u.id
		WHERE e.id=$1 AND e.deleted_at IS NULL`

	var e Event
	err := database.DB.QueryRow(query, id).Scan(
		&e.ID, &e.Name, &e.Slug, &e.Category, &e.Status,
		&e.StartDate, &e.EndDate, &e.Description, &e.Terms,
		&e.Location, &e.Province, &e.City, &e.Zip, &e.GoogleMapEmbed,
		&e.SeoTitle, &e.SeoDescription,
		&e.OrganizerName, &e.BannerPath, &e.ThumbnailPath, &e.OrganizerLogoPath,
		&e.YoutubeLink,
		&e.OrganizerTax, &e.OrganizerTaxType, &e.AdminFee, &e.AdminFeeType, &e.PPN, &e.PPNType,
		&e.ResellerFeeType, &e.ResellerFeeValue, &e.OrganizerFeeOnlineType, &e.OrganizerFeeOnline,
		&e.OrganizerFeeResellerType, &e.OrganizerFeeReseller,
		&e.PgFee, &e.PgFeeType, &e.PgFeeBank, &e.PgFeeQris,
		&e.CreatedAt,
		&e.OrganizerID,
	)
	if err != nil {
		return nil, err
	}
	return &e, nil
}

func GetPublishedEvents() ([]Event, error) {
	rows, err := database.DB.Query(`
		SELECT e.id, e.name, e.slug, e.category, e.status, e.banner_path, e.thumbnail_path, e.start_date, e.end_date, e.description, e.location, e.city, e.youtube_link, e.organizer_tax, e.organizer_tax_type, e.admin_fee, e.admin_fee_type, e.ppn, e.ppn_type, e.reseller_fee_type, e.reseller_fee_value, e.organizer_fee_online_type, e.organizer_fee_online, e.organizer_fee_reseller_type, e.organizer_fee_reseller, e.created_at,
		       COALESCE(MIN(t.price), 0) as min_price,
		       COALESCE(u.organizer_name, e.organizer_name) as organizer_name,
		       COALESCE(u.profile_photo_path, e.organizer_logo_path) as organizer_logo_path
		FROM events e
		LEFT JOIN tickets t ON e.id = t.event_id AND t.deleted_at IS NULL
		LEFT JOIN users u ON e.organizer_id = u.id
		WHERE (e.status = 'published' OR e.status = 'active') AND e.deleted_at IS NULL 
		GROUP BY e.id, u.id
		ORDER BY e.created_at DESC
	`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	events := []Event{} // Initialize as empty slice to return [] instead of null in JSON
	for rows.Next() {
		var e Event
		err := rows.Scan(
			&e.ID, &e.Name, &e.Slug, &e.Category, &e.Status,
			&e.BannerPath, &e.ThumbnailPath,
			&e.StartDate, &e.EndDate, &e.Description,
			&e.Location, &e.City,
			&e.YoutubeLink,
			&e.OrganizerTax, &e.OrganizerTaxType, &e.AdminFee, &e.AdminFeeType, &e.PPN, &e.PPNType,
			&e.ResellerFeeType, &e.ResellerFeeValue, &e.OrganizerFeeOnlineType, &e.OrganizerFeeOnline,
			&e.OrganizerFeeResellerType, &e.OrganizerFeeReseller,
			&e.CreatedAt,
			&e.MinPrice,
			&e.OrganizerName,
			&e.OrganizerLogoPath,
		)
		if err != nil {
			return nil, err
		}
		events = append(events, e)
	}
	return events, nil
}

func GetEventBySlug(slug string) (*Event, error) {
	query := `
		SELECT 
			e.id, e.name, e.slug, e.category, e.status, e.start_date, e.end_date, e.description, e.terms,
			e.location, e.province, e.city, e.zip, e.google_map_embed,
			e.seo_title, e.seo_description,
			COALESCE(u.organizer_name, e.organizer_name) as organizer_name, 
			e.banner_path, e.thumbnail_path, 
			COALESCE(u.profile_photo_path, e.organizer_logo_path) as organizer_logo_path,
			e.youtube_link,
			e.organizer_tax, e.organizer_tax_type, e.admin_fee, e.admin_fee_type, e.ppn, e.ppn_type,
			e.reseller_fee_type, e.reseller_fee_value, e.organizer_fee_online_type, e.organizer_fee_online,
			e.organizer_fee_reseller_type, e.organizer_fee_reseller,
			e.pg_fee, e.pg_fee_type, e.pg_fee_bank, e.pg_fee_qris,
			e.created_at,
			e.organizer_id
		FROM events e
		LEFT JOIN users u ON e.organizer_id = u.id
		WHERE e.slug=$1 AND e.deleted_at IS NULL`
	var e Event
	err := database.DB.QueryRow(query, slug).Scan(
		&e.ID, &e.Name, &e.Slug, &e.Category, &e.Status,
		&e.StartDate, &e.EndDate, &e.Description, &e.Terms,
		&e.Location, &e.Province, &e.City, &e.Zip, &e.GoogleMapEmbed,
		&e.SeoTitle, &e.SeoDescription,
		&e.OrganizerName, &e.BannerPath, &e.ThumbnailPath, &e.OrganizerLogoPath,
		&e.YoutubeLink,
		&e.OrganizerTax, &e.OrganizerTaxType, &e.AdminFee, &e.AdminFeeType, &e.PPN, &e.PPNType,
		&e.ResellerFeeType, &e.ResellerFeeValue, &e.OrganizerFeeOnlineType, &e.OrganizerFeeOnline,
		&e.OrganizerFeeResellerType, &e.OrganizerFeeReseller,
		&e.PgFee, &e.PgFeeType, &e.PgFeeBank, &e.PgFeeQris,
		&e.CreatedAt,
		&e.OrganizerID,
	)
	if err != nil {
		return nil, err
	}
	return &e, nil
}

func CountActiveEvents(organizerID *int) (int, error) {
	var count int
	query := `SELECT count(*) FROM events WHERE deleted_at IS NULL`
	var err error
	if organizerID != nil {
		query += ` AND organizer_id = $1`
		err = database.DB.QueryRow(query, *organizerID).Scan(&count)
	} else {
		err = database.DB.QueryRow(query).Scan(&count)
	}
	if err != nil {
		fmt.Printf("[models.CountActiveEvents] Error: %v\n", err)
	}
	return count, err
}

func SumPaidRevenue(organizerID *int) (float64, error) {
	var sum float64
	query := `SELECT COALESCE(SUM(total_price), 0)::FLOAT8 FROM transactions WHERE status = 'paid'`
	var err error
	if organizerID != nil {
		// This requires transactions to be linked to events which are linked to organizers
		query = `
			SELECT COALESCE(SUM(t.total_price), 0)::FLOAT8 
			FROM transactions t
			JOIN events e ON t.event_id = e.id
			WHERE t.status = 'paid' AND e.organizer_id = $1`
		err = database.DB.QueryRow(query, *organizerID).Scan(&sum)
	} else {
		err = database.DB.QueryRow(query).Scan(&sum)
	}
	if err != nil {
		fmt.Printf("[models.SumPaidRevenue] Error: %v\n", err)
	}
	return sum, err
}

func CountPaidTickets(organizerID *int) (int, error) {
	var count int
	query := `SELECT COALESCE(SUM(quantity), 0)::BIGINT FROM transactions WHERE status = 'paid'`
	var err error
	if organizerID != nil {
		query = `
			SELECT COALESCE(SUM(t.quantity), 0)::BIGINT 
			FROM transactions t
			JOIN events e ON t.event_id = e.id
			WHERE t.status = 'paid' AND e.organizer_id = $1`
		err = database.DB.QueryRow(query, *organizerID).Scan(&count)
	} else {
		err = database.DB.QueryRow(query).Scan(&count)
	}
	if err != nil {
		fmt.Printf("[models.CountPaidTickets] Error: %v\n", err)
	}
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
