package admin

import (
	"encoding/json"
	"event-backend/internal/models"
	"net/http"
)

func ListSettings(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	settings, err := models.GetAllSettings()
	if err != nil {
		http.Error(w, "Failed to fetch settings: "+err.Error(), http.StatusInternalServerError)
		return
	}

	// Transform to map for easier frontend consumption? Or list?
	// Laravel usually returns list of objects or key-value map.
	// Let's return list for now, or map key->value.
	// Map is easier for "Settings" page.

	res := make(map[string]string)
	for _, s := range settings {
		res[s.Key] = s.Value
	}

	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(res)
}

func UpdateSettings(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPut && r.Method != http.MethodPost {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	var req map[string]string
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Invalid body", http.StatusBadRequest)
		return
	}

	for key, value := range req {
		if err := models.UpdateSetting(key, value); err != nil {
			http.Error(w, "Failed to update setting "+key, http.StatusInternalServerError)
			return
		}
	}

	w.WriteHeader(http.StatusOK)
	json.NewEncoder(w).Encode(map[string]string{"status": "updated"})
}
