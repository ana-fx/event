package utils

import (
	"fmt"
	"io"
	"mime/multipart"
	"os"
	"path/filepath"
	"time"
)

// UploadFile saves a multipart file to the specified directory and returns the relative path or error.
func UploadFile(file multipart.File, header *multipart.FileHeader, destDir string) (string, error) {
	// Create directory if not exists
	// fullDestDir := filepath.Join("uploads", destDir) // Removed unused
	// Ensure uploads directory exists in root or handled by static server
	// We'll write to "./public/uploads/..." or similar.
	// Assuming backend serves static from "./storage" or similar.
	// Let's settle on "./storage/uploads/{destDir}"

	uploadPath := filepath.Join("storage", "uploads", destDir)
	if err := os.MkdirAll(uploadPath, os.ModePerm); err != nil {
		return "", err
	}

	// Generate unique filename
	ext := filepath.Ext(header.Filename)
	filename := fmt.Sprintf("%d%s", time.Now().UnixNano(), ext)
	fullPath := filepath.Join(uploadPath, filename)

	// Create file
	dst, err := os.Create(fullPath)
	if err != nil {
		return "", err
	}
	defer dst.Close()

	// Copy content
	if _, err := io.Copy(dst, file); err != nil {
		return "", err
	}

	// Return relative path for DB
	// e.g. "/uploads/banners/12345.jpg" (assuming static handler serves /uploads mapped to storage/uploads)
	return fmt.Sprintf("/uploads/%s/%s", destDir, filename), nil
}

func Slugify(s string) string {
	// Basic placeholder. TODO: Use a proper regex or library.
	return s
}
