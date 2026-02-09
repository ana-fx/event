-- +goose Up
-- +goose StatementBegin
ALTER TABLE transactions 
ADD COLUMN IF NOT EXISTS city VARCHAR(255),
ADD COLUMN IF NOT EXISTS nik VARCHAR(255),
ADD COLUMN IF NOT EXISTS gender VARCHAR(20);

-- If we want them to be NOT NULL, we should probably set a default or update existing rows first.
-- For now, let's just make them available.
-- +goose StatementEnd

-- +goose Down
-- +goose StatementBegin
ALTER TABLE transactions 
DROP COLUMN IF EXISTS city,
DROP COLUMN IF EXISTS nik,
DROP COLUMN IF EXISTS gender;
-- +goose StatementEnd
