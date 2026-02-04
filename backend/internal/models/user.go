package models

import (
	"database/sql"
	"errors"
	"event-backend/internal/database"
	"time"
)

type User struct {
	ID       int    `json:"id"`
	Name     string `json:"name"`
	Email    string `json:"email"`
	Password string `json:"password,omitempty"`
	Role     string `json:"role"`
	IsActive bool   `json:"is_active"`
}

func GetUserByEmail(email string) (*User, error) {
	stmt := `SELECT id, name, email, password, is_active FROM users WHERE email = $1`
	row := database.DB.QueryRow(stmt, email)

	var user User
	// SQLite stores booleans as 0/1 integers often, but go-sqlite3 might handle bool.
	// But in Laravel migrations, it's safer to check how it was created.
	// Assuming standard scan works.
	err := row.Scan(&user.ID, &user.Name, &user.Email, &user.Password, &user.IsActive)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, errors.New("user not found")
		}
		return nil, err
	}

	return &user, nil
}

func GetAllUsers() ([]User, error) {
	rows, err := database.DB.Query(`SELECT id, name, email, is_active FROM users ORDER BY id DESC`)
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

func CreateUser(u *User) error {
	query := `INSERT INTO users (name, email, password, role, is_active, created_at, updated_at) VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING id`
	return database.DB.QueryRow(query, u.Name, u.Email, u.Password, u.Role, u.IsActive, time.Now(), time.Now()).Scan(&u.ID)
}

func UpdateUser(u *User) error {
	// Password update should be handled carefully (hashed) handler side.
	// Here we assume if Password is set, we update it.
	var err error
	if u.Password != "" {
		query := `UPDATE users SET name=$1, email=$2, password=$3, is_active=$4, updated_at=$5 WHERE id=$6`
		_, err = database.DB.Exec(query, u.Name, u.Email, u.Password, u.IsActive, time.Now(), u.ID)
	} else {
		query := `UPDATE users SET name=$1, email=$2, is_active=$3, updated_at=$4 WHERE id=$5`
		_, err = database.DB.Exec(query, u.Name, u.Email, u.IsActive, time.Now(), u.ID)
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
	return count, err
}
