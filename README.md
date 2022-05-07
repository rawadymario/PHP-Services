# Common Services Used in all Projects

To install Via Composer: `composer require rawadymario/services`

Packagist Link: [https://packagist.org/packages/rawadymario/services](https://packagist.org/packages/rawadymario/services)

---

## List of Files

### Default Translations

- Date
- Exceptions
- Global
- Number

### Exceptions

Customized Exception Classes.

`Check Exceptions Folder for the full list!`

### Helpers

- Currency Helper
- Date Helper
- Helper
- Lang Helper
- MachineInfoHelper
- Media Helper
- Translate Helper
  - Supported Languages
    - English
    - Arabic
    - French
- Upload Helper
- Validator Helper

> To Revisit:

- MachineInfoHelper

### Models

- Code
- Currency Position
- Date Formats
- Date Format Types
- Date Types
- HTTP Codes
- Lang
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