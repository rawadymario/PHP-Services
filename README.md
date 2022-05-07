# Common Services Used in all Projects

To install Via Composer: `composer require rawadymario/services`

Packagist Link: [https://packagist.org/packages/rawadymario/services](https://packagist.org/packages/rawadymario/services)

---

## List of Files

### Exceptions

> Customized Exception Classes.

- Check Exceptions Folder for the full list

### Helpers

- Helper
- Validator

### Languages

> Supported Languages: (English, Arabic, French)

- Classes
  - Language
  - Translate
- Mappings
  - Date
  - Exceptions
  - Global
  - Number
- Models
  - Lang

### Models

- Code
- Currency Position
- Date Formats
- Date Format Types
- Date Types
- HTTP Codes
- Status

## Default Functions

1. Language
   1. SetVariableDefault(`string $var`)
   2. SetVariableActive(`string $var`)
2. MediaHelper
   1. SetVariableMediaFolder(`string $var`)
   2. SetVariableUploadDir(`string $var`)
   3. SetVariableMediaRoot(`string $var`)
   4. SetVariableWebsiteVersion(`string $var`)
3. Translate
   1. AddDefaults()
   2. AddCustomDir(`string $customDir`)