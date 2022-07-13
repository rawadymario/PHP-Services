# Common Services Used in all Projects

To install Via Composer: `composer require rawadymario/php-services`

Packagist Link: [https://packagist.org/packages/rawadymario/php-services](https://packagist.org/packages/rawadymario/php-services)

---

## Structure

### Exceptions

> Customized Exception Classes.

- Check Exceptions Folder for the full list

### Helpers

- Cookie
- Helper
- Machine Info
- MetaSeo
- Script
- ServerCache
- Style
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

### Renders

- Accordion
- Tabs

## Default Functions

> The below functions are used to set the default values before starting to code

### Helpers

- #### Cookie

  - SetExpireInUnix(`int $unix`)
  - SetExpireInDays(`int $days`)
  - SetPrefix(`string $prefix`)
  - SetPath(`string $path`)
  - SetDomain(`string $domain`)
  - SetSecure(`bool $secure`)
  - SetHttpOnly(`bool $httpOnly`)

- #### MetaSeo

  - AddToMetaArray(`string $key, array $array`)
  - AddToPreHeadArray(`string $key, string $text`)
  - AddToPostHeadArray(`string $key, string $text`)
  - SetClientName(`string $var`)
  - SetPreTitle(`string $var`)
  - SetPostTitle(`string $var`)
  - SetTitle(`string $var`)
  - SetAuthor(`string $var`)
  - SetKeywords(`string $var`)
  - SetDescription(`string $var`)
  - SetPhoto(`string $var`)
  - SetUrl(`string $var`)
  - SetRobots(`bool $isLive`)
  - SetRevisitAfter(`string $var`)
  - SetFavicon(`string $var`)
  - SetContentType(`string $var`)
  - SetXuaCompatible(`string $var`)
  - SetViewport(`string $var`)
  - SetGoolgeSiteVerification(`string $var`)
  - SetCopyright(`string $var`)
  - SetAppleMobileWebAppCapable(`string $var`)
  - SetAppleMobileWebAppStatusBarStyle(`string $var`)
  - SetFacebookType(`string $var`)
  - SetFacebookAppId(`string $var`)
  - SetFacebookAdmins(`string $var`)
  - SetTwitterCard(`string $var`)

- #### Script

  - AddFile(`string $key, string $file`)
  - AddScript(`string $key, string $script`)
  - GetFilesIncludes()

- #### ServerCache

  - SetVersion(`string $version`)
  - SetCacheFolder(`string $cacheFolder`)
  - SetVersionFolder(`string $versionFolder`)

- #### Style

  - AddFile(`string $key, string $file`)
  - AddStyle(`string $key, string $style`)
  - GetFilesIncludes()

### Microservices

- #### Language
  - Helpers
    - Language
      - SetDefault(`string $var`)
      - SetActive(`string $var`)
    - Translate
      - AddDefaults()
      - AddCustomDir(`string $customDir`)

- #### Media
  - SetMediaFolder(`string $var`)
  - SetUploadDir(`string $var`)
  - SetMediaRoot(`string $var`)
  - SetWebsiteVersion(`string $var`)

## To Revisit

- MachineInfo
  - GetIpInfo
