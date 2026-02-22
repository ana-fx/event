package models

import (
	"database/sql"
	"errors"
	"event-backend/internal/database"
	"fmt"
	"time"
)

var SecretKey = []byte("your_secret_key_change_me") // TODO: Move to .env

type User struct {
	ID               int            `json:"id"`
	Name             string         `json:"name"`
	Email            string         `json:"email"`
	Password         string         `json:"password,omitempty"`
	Role             string         `json:"role"`
	IsActive         bool           `json:"is_active"`
	Username         sql.NullString `json:"username"`
	OrganizerName    sql.NullString `json:"organizer_name"`
	AboutUs          sql.NullString `json:"about_us"`
	Phone            sql.NullString `json:"phone"`
	Province         sql.NullString `json:"province"`
	City             sql.NullString `json:"city"`
	ZipCode          sql.NullString `json:"zip_code"`
	Address          sql.NullString `json:"address"`
	ProfilePhotoPath sql.NullString `json:"profile_photo_path"`
}

type contextKey string

const UserIDKey contextKey = "userID"

func GetUserByEmail(email string) (*User, error) {
	stmt := `SELECT id, name, email, password, role, is_active, username, organizer_name, about_us, phone, province, city, zip_code, address, profile_photo_path FROM users WHERE email = $1`
	row := database.DB.QueryRow(stmt, email)

	var user User
	err := row.Scan(
		&user.ID, &user.Name, &user.Email, &user.Password, &user.Role, &user.IsActive,
		&user.Username, &user.OrganizerName, &user.AboutUs, &user.Phone, &user.Province, &user.City, &user.ZipCode, &user.Address, &user.ProfilePhotoPath,
	)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, errors.New("user not found")
		}
		return nil, err
	}

	return &user, nil
}

func GetUserByID(id int) (*User, error) {
	stmt := `SELECT id, name, email, role, is_active, username, organizer_name, about_us, phone, province, city, zip_code, address, profile_photo_path FROM users WHERE id = $1`
	row := database.DB.QueryRow(stmt, id)

	var user User
	err := row.Scan(
		&user.ID, &user.Name, &user.Email, &user.Role, &user.IsActive,
		&user.Username, &user.OrganizerName, &user.AboutUs, &user.Phone, &user.Province, &user.City, &user.ZipCode, &user.Address, &user.ProfilePhotoPath,
	)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, errors.New("user not found")
		}
		return nil, err
	}

	return &user, nil
}

func GetAllUsers() ([]User, error) {
	rows, err := database.DB.Query(`SELECT id, name, email, role, is_active, username, organizer_name, about_us, phone, province, city, zip_code, address, profile_photo_path FROM users ORDER BY id DESC`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var users []User
	for rows.Next() {
		var u User
		err := rows.Scan(
			&u.ID, &u.Name, &u.Email, &u.Role, &u.IsActive,
			&u.Username, &u.OrganizerName, &u.AboutUs, &u.Phone, &u.Province, &u.City, &u.ZipCode, &u.Address, &u.ProfilePhotoPath,
		)
		if err != nil {
			return nil, err
		}
		users = append(users, u)
	}
	return users, nil
}

func CreateUser(u *User) error {
	query := `INSERT INTO users (name, email, password, role, is_active, username, organizer_name, about_us, phone, province, city, zip_code, address, profile_photo_path, created_at, updated_at) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13, $14, $15, $16) RETURNING id`
	return database.DB.QueryRow(query,
		u.Name, u.Email, u.Password, u.Role, u.IsActive,
		u.Username, u.OrganizerName, u.AboutUs, u.Phone, u.Province, u.City, u.ZipCode, u.Address, u.ProfilePhotoPath,
		time.Now(), time.Now(),
	).Scan(&u.ID)
}

func UpdateUser(u *User) error {
	// Password update should be handled carefully (hashed) handler side.
	// Here we assume if Password is set, we update it.
	var err error
	if u.Password != "" {
		query := `UPDATE users SET name=$1, email=$2, password=$3, is_active=$4, username=$5, organizer_name=$6, about_us=$7, phone=$8, province=$9, city=$10, zip_code=$11, address=$12, profile_photo_path=$13, updated_at=$14 WHERE id=$15`
		_, err = database.DB.Exec(query, u.Name, u.Email, u.Password, u.IsActive, u.Username, u.OrganizerName, u.AboutUs, u.Phone, u.Province, u.City, u.ZipCode, u.Address, u.ProfilePhotoPath, time.Now(), u.ID)
	} else {
		query := `UPDATE users SET name=$1, email=$2, is_active=$3, username=$4, organizer_name=$5, about_us=$6, phone=$7, province=$8, city=$9, zip_code=$10, address=$11, profile_photo_path=$12, updated_at=$13 WHERE id=$14`
		_, err = database.DB.Exec(query, u.Name, u.Email, u.IsActive, u.Username, u.OrganizerName, u.AboutUs, u.Phone, u.Province, u.City, u.ZipCode, u.Address, u.ProfilePhotoPath, time.Now(), u.ID)
	}
	return err
}

func DeleteUser(id int) error {
	_, err := database.DB.Exec("DELETE FROM users WHERE id=$1", id)
	return err
}
func CountUsers() (int, error) {
	var count int
	err := database.DB.QueryRow(`SELECT count(*) FROM users`).Scan(&count)
	if err != nil {
		fmt.Printf("[models.CountUsers] Error: %v\n", err)
	}
	return count, err
}
