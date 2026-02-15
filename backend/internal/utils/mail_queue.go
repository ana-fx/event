package utils

import (
	"log"
)

type EmailJob struct {
	To      string
	Subject string
	Body    string
}

var emailQueue chan EmailJob

// InitMailQueue initializes the email queue and starts workers
func InitMailQueue(bufferSize int, numWorkers int) {
	emailQueue = make(chan EmailJob, bufferSize)

	for i := 1; i <= numWorkers; i++ {
		go emailWorker(i)
	}
	log.Printf("MailQueue initialized with %d workers and buffer of %d", numWorkers, bufferSize)
}

func emailWorker(id int) {
	log.Printf("MailWorker %d started", id)
	for job := range emailQueue {
		err := SendEmail(job.To, job.Subject, job.Body)
		if err != nil {
			log.Printf("MailWorker %d: [ERROR] Failed to send email to %s: %v", id, job.To, err)
		} else {
			log.Printf("MailWorker %d: [SUCCESS] Email sent to %s", id, job.To)
		}
	}
}

// EnqueueEmail adds an email to the queue
func EnqueueEmail(to string, subject string, body string) {
	if emailQueue == nil {
		log.Println("[WARNING] MailQueue not initialized. Sending email synchronously as fallback.")
		go func() {
			err := SendEmail(to, subject, body)
			if err != nil {
				log.Printf("[ERROR] Fallback email failed to %s: %v", to, err)
			}
		}()
		return
	}

	job := EmailJob{
		To:      to,
		Subject: subject,
		Body:    body,
	}

	select {
	case emailQueue <- job:
		// Job enqueued successfully
	default:
		log.Printf("[WARNING] MailQueue is full. Dropping email to %s or consider increasing buffer size.", to)
	}
}
