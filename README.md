# Common Services Used in all Projects

To install Via Composer: `composer require rawadymario/php-services`

Packagist Link: [https://packagist.org/packages/rawadymario/php-services](https://packagist.org/packages/rawadymario/php-services)

---

## List of Files

### Exceptions

> Customized Exception Classes.

- Check Exceptions Folder for the full list

### Helpers

- Helper
- Machine Info
- Validator

### Microservices

#### Accounting

- Helpers
  - Currency
- Models
  - Currency Position

#### Date

- Helpers
  - Date
- Models
  - Date Formats
  - Date Format Types
  - Date Types

#### Language

> Supported Languages: (English, Arabic, French)

- Helpers
  - Language
  - Translate
- Mappings
  - Date
  - Exceptions
  - Global
  - Number
- Models
  - Lang

#### Media

- Helpers
  - Media
- Models
  - Facebook Image
  - Image

### Models

- Code
- HTTP Codes
- Status

## Default Functions

1. Language
   1. SetVariableDefault(`string $var`)
   2. SetVariableActive(`string $var`)
2. Media
   1. SetVariableMediaFolder(`string $var`)
   2. SetVariableUploadDir(`string $var`)
   3. SetVariableMediaRoot(`string $var`)
   4. SetVariableWebsiteVersion(`string $var`)
3. Translate
   1. AddDefaults()
   2. AddCustomDir(`string $customDir`)
