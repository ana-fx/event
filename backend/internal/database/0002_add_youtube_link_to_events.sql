-- +goose Up
-- +goose StatementBegin
ALTER TABLE events ADD COLUMN youtube_link TEXT NULL;
-- +goose StatementEnd

-- +goose Down
-- +goose StatementBegin
ALTER TABLE events DROP COLUMN youtube_link;
-- +goose StatementEnd
