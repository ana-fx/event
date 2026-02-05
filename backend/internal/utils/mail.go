package utils

import (
	"fmt"
	"net/smtp"
	"os"
)

func SendEmail(to string, subject string, body string) error {
	from := os.Getenv("SMTP_FROM")
	pass := os.Getenv("SMTP_PASS")
	user := os.Getenv("SMTP_USER")
	host := os.Getenv("SMTP_HOST")
	port := os.Getenv("SMTP_PORT")

	msg := "From: " + from + "\n" +
		"To: " + to + "\n" +
		"Subject: " + subject + "\n" +
		"MIME-version: 1.0;\n" +
		"Content-Type: text/html; charset=\"UTF-8\";\n\n" +
		body

	auth := smtp.PlainAuth("", user, pass, host)
	err := smtp.SendMail(host+":"+port, auth, from, []string{to}, []byte(msg))
	if err != nil {
		return fmt.Errorf("failed to send email: %w", err)
	}

	return nil
}

func GetTicketTemplate(name string, eventName string, ticketCode string) string {
	return fmt.Sprintf(`
		<div style="font-family: Arial, sans-serif; padding: 20px; border: 1px solid #eee; border-radius: 10px; max-width: 600px; margin: auto;">
			<h1 style="color: #2563eb;">Your Ticket is Ready!</h1>
			<p>Hi %s,</p>
			<p>Thank you for your purchase. Here is your ticket for <strong>%s</strong>.</p>
			<div style="background: #f8fafc; padding: 20px; border-radius: 8px; text-align: center; margin: 20px 0;">
				<p style="text-transform: uppercase; letter-spacing: 2px; color: #64748b; margin-bottom: 5px;">Ticket Code</p>
				<h2 style="font-size: 32px; margin: 0; color: #0f172a;">%s</h2>
			</div>
			<p style="color: #64748b; font-size: 14px;">Please present this code at the entrance to redeem your ticket.</p>
		</div>
	`, name, eventName, ticketCode)
}
