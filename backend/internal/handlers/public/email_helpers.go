package public

import (
	"event-backend/internal/models"
	"event-backend/internal/utils"
	"fmt"
	"log"
	"os"
)

func triggerSuccessEmail(trx *models.Transaction) {
	event, err := models.GetEventByID(trx.EventID)
	if err != nil {
		log.Printf("Email error: failed to fetch event %d: %v", trx.EventID, err)
		return
	}

	ticketName := "Ticket"
	if len(trx.Items) > 0 {
		ticketName = trx.Items[0].Name
		if len(trx.Items) > 1 {
			ticketName += fmt.Sprintf(" (+%d others)", len(trx.Items)-1)
		}
	} else if trx.TicketID.Valid {
		ticket, _ := models.GetTicketByID(int(trx.TicketID.Int64))
		if ticket != nil {
			ticketName = ticket.Name
		}
	}

	totalPriceStr := fmt.Sprintf("IDR %.0f", trx.TotalPrice)

	eventImage := ""
	if event.ThumbnailPath != nil {
		assetURL := os.Getenv("ASSET_URL")
		if assetURL == "" {
			assetURL = "http://localhost:8080"
		}
		eventImage = fmt.Sprintf("%s/%s", assetURL, *event.ThumbnailPath)
	}

	body := utils.GetSuccessWithTicketTemplate(trx.Name, trx.Email, trx.Phone, trx.NIK, trx.Gender, trx.City, event.Name, ticketName, int(trx.Quantity.Int64), totalPriceStr, trx.Code, eventImage)
	utils.EnqueueEmail(trx.Email, "Success! Your Ticket for "+event.Name, body)
}
