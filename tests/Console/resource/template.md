# /foo
Test description
### Path-Parameters
<a name="ObjectId"></a>
##### path
* Required: `["fooId"]`
* Properties:
  * `fooId`:
    * Type: string

## GET 
A long **Test** description
### Query-Parameters
<a name="ObjectId"></a>
##### query
* Required: `["bar"]`
* Properties:
  * `foo`:
    * Type: string
  * `bar`:
    * Type: string
  * `baz`:
    * Type: string
    * Enum: `["foo","bar"]`
  * `boz`:
    * Type: string
    * Pattern: `[A-z]+`
  * `integer`:
    * Type: integer
  * `number`:
    * Type: number
  * `date`:
    * Type: string
  * `boolean`:
    * Type: boolean
  * `string`:
    * Type: string

### Request
<a name="ObjectId"></a>
##### Object
A canonical song
* Required: `["title","artist"]`
* Properties:
  * `artist`:
    * Type: string
  * `title`:
    * Type: string


### Response - 200:
<a name="ObjectId"></a>
##### Object
A canonical song
* Required: `["title","artist"]`
* Properties:
  * `artist`:
    * Type: string
  * `title`:
    * Type: string






