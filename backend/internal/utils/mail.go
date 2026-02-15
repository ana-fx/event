package utils

import (
	"fmt"
	"net/smtp"
	"net/url"
	"os"
)

func SendEmail(to string, subject string, body string) error {
	from := os.Getenv("SMTP_FROM")
	fromName := os.Getenv("SMTP_FROM_NAME")
	pass := os.Getenv("SMTP_PASS")
	user := os.Getenv("SMTP_USER")
	host := os.Getenv("SMTP_HOST")
	port := os.Getenv("SMTP_PORT")

	fromHeader := from
	if fromName != "" {
		fromHeader = fmt.Sprintf("%s <%s>", fromName, from)
	}

	msg := "From: " + fromHeader + "\n" +
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

func GetPaymentRequiredTemplate(name, email, phone, nik, gender, city, eventName, ticketName string, quantity int, totalPrice, paymentLink, eventImage string) string {
	headerImg := ""
	if eventImage != "" {
		headerImg = fmt.Sprintf(`<img src="%s" style="width: 100%%; max-width: 600px; height: auto; display: block; border-radius: 24px 24px 0 0; margin-bottom: -24px;" alt="%s">`, eventImage, eventName)
	}

	return fmt.Sprintf(`
		<div style="font-family: 'Inter', sans-serif; max-width: 600px; margin: 40px auto;">
			%s
			<div style="padding: 40px; background-color: #ffffff; border-radius: 24px; border: 1px solid #f1f5f9; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); position: relative; z-index: 1;">
				<div style="text-align: center; margin-bottom: 32px;">
					<div style="background-color: #fef2f2; color: #dc2626; display: inline-block; padding: 8px 16px; border-radius: 9999px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px;">Payment Required</div>
					<h1 style="color: #0f172a; margin: 0; font-size: 24px; font-weight: 900; letter-spacing: -0.025em; text-transform: uppercase;">Complete Your Order</h1>
				</div>
				
				<p style="color: #475569; font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hi <strong>%s</strong>,</p>
				<p style="color: #475569; font-size: 16px; line-height: 1.6; margin-bottom: 32px;">Thank you for registering for <strong>%s</strong>. To secure your spot, please complete the payment for your order.</p>

				<!-- Registration Details -->
				<div style="margin-bottom: 32px; border: 1px solid #f1f5f9; border-radius: 16px; padding: 24px;">
					<p style="margin: 0 0 16px 0; color: #94a3b8; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;">Buyer Information</p>
					<table width="100%%" border="0" cellspacing="0" cellpadding="0" style="font-size: 14px; line-height: 2;">
						<tr>
							<td width="40%%" style="color: #94a3b8;">Full Name</td>
							<td style="color: #1e293b; font-weight: 600;">%s</td>
						</tr>
						<tr>
							<td style="color: #94a3b8;">Email</td>
							<td style="color: #1e293b; font-weight: 600;">%s</td>
						</tr>
						<tr>
							<td style="color: #94a3b8;">Phone</td>
							<td style="color: #1e293b; font-weight: 600;">%s</td>
						</tr>
						<tr>
							<td style="color: #94a3b8;">NIK/ID Number</td>
							<td style="color: #1e293b; font-weight: 600;">%s</td>
						</tr>
						<tr>
							<td style="color: #94a3b8;">Gender</td>
							<td style="color: #1e293b; font-weight: 600;">%s</td>
						</tr>
						<tr>
							<td style="color: #94a3b8;">City</td>
							<td style="color: #1e293b; font-weight: 600;">%s</td>
						</tr>
					</table>
				</div>
				
				<!-- Payment Summary -->
				<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 32px; border-radius: 20px; margin-bottom: 32px;">
					<table width="100%%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td style="padding-bottom: 16px; border-bottom: 1px dashed #e2e8f0;">
								<p style="margin: 0; color: #94a3b8; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;">Ticket Details</p>
								<p style="margin: 4px 0 0 0; color: #1e293b; font-size: 15px; font-weight: 700;">%s (x%d)</p>
							</td>
						</tr>
						<tr>
							<td style="padding-top: 16px;">
								<table width="100%%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td align="left" style="color: #64748b; font-size: 14px; font-family: 'Inter', sans-serif;">Total Amount</td>
										<td align="right" style="color: #0f172a; font-size: 20px; font-weight: 900; font-family: 'Inter', sans-serif;">%s</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
				
				<div style="text-align: center; margin-bottom: 32px;">
					<a href="%s" style="background-color: #0f172a; color: #ffffff; padding: 20px 40px; border-radius: 16px; text-decoration: none; font-weight: 800; font-size: 14px; text-transform: uppercase; letter-spacing: 0.15em; display: inline-block; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">Pay Now</a>
				</div>
				
				<p style="color: #94a3b8; font-size: 13px; text-align: center; line-height: 1.5;">If you have already paid, please ignore this email. Your ticket will be issued once the payment is verified.</p>
				
				<div style="text-align: center; border-top: 1px solid #f1f5f9; margin-top: 32px;">
					<p style="color: #94a3b8; font-size: 12px; margin-top: 24px;">Sent with &hearts; by Anttix Event Platform</p>
				</div>
			</div>
		</div>
	`, headerImg, name, eventName, name, email, phone, nik, gender, city, ticketName, quantity, totalPrice, paymentLink)
}

func GetSuccessWithTicketTemplate(name, email, phone, nik, gender, city, eventName, ticketName string, quantity int, totalPrice, orderID, eventImage string) string {
	qrCodeURL := fmt.Sprintf("https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=%s", url.QueryEscape(orderID))
	headerImg := ""
	if eventImage != "" {
		headerImg = fmt.Sprintf(`<img src="%s" style="width: 100%%; max-width: 600px; height: auto; display: block; border-radius: 24px 24px 0 0; margin-bottom: -24px;" alt="%s">`, eventImage, eventName)
	}

	return fmt.Sprintf(`
		<div style="font-family: 'Inter', sans-serif; max-width: 600px; margin: 40px auto;">
			%s
			<div style="padding: 40px; background-color: #ffffff; border-radius: 24px; border: 1px solid #f1f5f9; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); position: relative; z-index: 1;">
				<div style="text-align: center; margin-bottom: 32px;">
					<div style="background-color: #f0fdf4; color: #16a34a; display: inline-block; padding: 8px 16px; border-radius: 9999px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 16px;">Order Successful</div>
					<h1 style="color: #0f172a; margin: 0; font-size: 24px; font-weight: 900; letter-spacing: -0.025em; text-transform: uppercase;">Registration Confirmed</h1>
				</div>
				
				<p style="color: #475569; font-size: 16px; line-height: 1.6; margin-bottom: 24px;">Hi <strong>%s</strong>,</p>
				<p style="color: #475569; font-size: 16px; line-height: 1.6; margin-bottom: 32px;">Great news! Your payment has been confirmed. Your ticket for <strong>%s</strong> is ready and attached below. We look forward to seeing you at the event!</p>

				<!-- TICKET CARD (BARCODE/QR) -->
				<div style="background-color: #f8fafc; border: 2px dashed #e2e8f0; padding: 40px; border-radius: 24px; text-align: center; margin-bottom: 32px;">
					<p style="text-transform: uppercase; letter-spacing: 0.2em; color: #94a3b8; font-size: 10px; font-weight: 800; margin-bottom: 20px; margin-top: 0;">Your Entry Ticket</p>
					<div style="background-color: #ffffff; padding: 20px; display: inline-block; border-radius: 20px; border: 1px solid #f1f5f9; margin-bottom: 20px;">
						<img src="%s" width="150" height="150" alt="Ticket QR Code" style="display: block;">
					</div>
					<h2 style="font-size: 24px; margin: 0; color: #1e293b; font-weight: 900; letter-spacing: 0.1em; font-family: monospace;">%s</h2>
					<p style="color: #64748b; font-size: 12px; margin-top: 8px;">Show this QR code at the entrance</p>
				</div>

				<!-- Registration details -->
				<div style="margin-bottom: 32px; border: 1px solid #f1f5f9; border-radius: 16px; padding: 24px;">
					<p style="margin: 0 0 16px 0; color: #94a3b8; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;">Buyer Information</p>
					<table width="100%%" border="0" cellspacing="0" cellpadding="0" style="font-size: 14px; line-height: 2;">
						<tr>
							<td width="40%%" style="color: #94a3b8;">Full Name</td>
							<td style="color: #1e293b; font-weight: 600;">%s</td>
						</tr>
						<tr>
							<td style="color: #94a3b8;">Email</td>
							<td style="color: #1e293b; font-weight: 600;">%s</td>
						</tr>
						<tr>
							<td style="color: #94a3b8;">Phone</td>
							<td style="color: #1e293b; font-weight: 600;">%s</td>
						</tr>
						<tr>
							<td style="color: #94a3b8;">NIK/ID Number</td>
							<td style="color: #1e293b; font-weight: 600;">%s</td>
						</tr>
						<tr>
							<td style="color: #94a3b8;">Gender</td>
							<td style="color: #1e293b; font-weight: 600;">%s</td>
						</tr>
						<tr>
							<td style="color: #94a3b8;">City</td>
							<td style="color: #1e293b; font-weight: 600;">%s</td>
						</tr>
					</table>
				</div>
				
				<div style="background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 32px; border-radius: 20px; margin-bottom: 32px;">
					<table width="100%%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td style="padding-bottom: 16px; border-bottom: 1px dashed #e2e8f0;">
								<p style="margin: 0; color: #94a3b8; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;">Order ID: #%s</p>
								<p style="margin: 4px 0 0 0; color: #1e293b; font-size: 15px; font-weight: 700;">%s (x%d)</p>
							</td>
						</tr>
						<tr>
							<td style="padding-top: 16px;">
								<table width="100%%" border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td align="left" style="color: #64748b; font-size: 14px; font-family: 'Inter', sans-serif;">Amount Paid</td>
										<td align="right" style="color: #16a34a; font-size: 20px; font-weight: 900; font-family: 'Inter', sans-serif;">%s</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
				
				<div style="text-align: center; border-top: 1px solid #f1f5f9; margin-top: 32px;">
					<p style="color: #94a3b8; font-size: 12px; margin-top: 24px;">Sent with &hearts; by Anttix Event Platform</p>
				</div>
			</div>
		</div>
	`, headerImg, name, eventName, qrCodeURL, orderID, name, email, phone, nik, gender, city, orderID, ticketName, quantity, totalPrice)
}
