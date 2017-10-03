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
##### Song
A canonical song
* Required: `["title","artist"]`
* Properties:
  * `title`:
    * Type: string
  * `artist`:
    * Type: string
  * `length`:
    * Type: integer
  * `ratings`:
    * Type: array
    * __Items__:
    * [Rating](#ObjectId)

<a name="ObjectId"></a>
##### Rating
* Properties:
  * `author`:
    * Type: string
  * `rating`:
    * Type: integer
  * `text`:
    * Type: string



### Response - 200:
<a name="ObjectId"></a>
##### Song
A canonical song
* Required: `["title","artist"]`
* Properties:
  * `title`:
    * Type: string
  * `artist`:
    * Type: string
  * `length`:
    * Type: integer
  * `ratings`:
    * Type: array
    * __Items__:
    * [Rating](#ObjectId)

<a name="ObjectId"></a>
##### Rating
* Properties:
  * `author`:
    * Type: string
  * `rating`:
    * Type: integer
  * `text`:
    * Type: string







