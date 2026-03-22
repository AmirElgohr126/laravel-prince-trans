# CHANGELOG

All notable changes to the `astrotomic/translatable-migration-builder` package will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2026-03-22

### Added
- `Generate In Project` action in Preview to write files directly into Laravel app.
- Automatic seeder generation (`database/seeders/{Model}Seeder.php`).
- Repository generation pattern:
  - `{Model}Repository` with admin methods.
  - `Eloquent{Model}Repository` extends `App\\Repositories\\EloquentBaseRepository`.
- `seeder_path` configuration key.

### Changed
- Generated model now includes `$translatedAttributes` whenever translatable columns exist.
- Migration generator fixes for translation foreign key column and unique constraint output.
- Namespace generation fixes for model/repository outputs.

## [1.0.0] - 2024-03-22

### Added
- Initial release
- Visual UI-based migration builder
- Support for translatable columns
- Automatic translation table generation
- Live preview of migrations and models
- Model generator with Translatable trait integration
- Artisan command `php artisan translatable:builder`
- Web route `/translatable-builder`
- Configuration file with customizable options
- Full customization for table structure:
  - Custom primary keys
  - Custom foreign keys
  - Database engine and charset options
  - Soft deletes and timestamps
  - Column reordering
- Download functionality for migrations and models
- Copy-to-clipboard functionality in preview
- Comprehensive documentation and examples
- Support for multiple column types
- Support for indexes and constraints
- Livewire component-based UI

### Features Included
- 100% Livewire-based interactive interface
- Responsive design with Tailwind CSS
- Zero JavaScript required (Tailwind only)
- Clean separation of concerns (Builders, Generators, UI)
- Extensible architecture for future enhancements
- Production-ready code following Laravel best practices

## [1.1.0] - [Upcoming]

### Planned Features
- Batch column operations
- Column templates/presets
- Save and load builder configurations
- Migration history
- Direct database seeding options
- API endpoints for programmatic access
- Advanced validation rules generation
- Factory generator
- Test generation
- Migration versioning
