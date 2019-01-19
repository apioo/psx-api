# /foo/:name/:type
lorem ipsum
### Path-Parameters
<a name="[id]"></a>
##### path
* Required: `["name"]`
* Properties:
  * `name`:
    * Type: string
    * Pattern: `[A-z]+`
    * Max-Length: 16
  * `type`:
    * Type: string
    * Enum: `["foo","bar"]`

## GET 
Returns a collection
### Query-Parameters
<a name="[id]"></a>
##### query
* Required: `["startIndex"]`
* Properties:
  * `startIndex`:
    * Type: integer
    * Maximum: 32
  * `float`:
    * Type: number
  * `boolean`:
    * Type: boolean
  * `date`:
    * Type: string
    * Format: date
  * `datetime`:
    * Type: string
    * Format: date-time

### Response - 200:
<a name="[id]"></a>
##### collection
* Properties:
  * `entry`:
    * Type: array
    * __Items__:
    * [item](#[id])

<a name="[id]"></a>
##### item
* Properties:
  * `id`:
    * Type: integer
  * `userId`:
    * Type: integer
  * `title`:
    * Type: string
    * Pattern: `[A-z]+`
    * Min-Length: 3
    * Max-Length: 16
  * `date`:
    * Type: string
    * Format: date-time



## POST 
### Query-Parameters
<a name="[id]"></a>
##### query

### Request
<a name="[id]"></a>
##### item
* Required: `["title","date"]`
* Properties:
  * `id`:
    * Type: integer
  * `userId`:
    * Type: integer
  * `title`:
    * Type: string
    * Pattern: `[A-z]+`
    * Min-Length: 3
    * Max-Length: 16
  * `date`:
    * Type: string
    * Format: date-time


### Response - 201:
<a name="[id]"></a>
##### message
* Properties:
  * `success`:
    * Type: boolean
  * `message`:
    * Type: string


## PUT 
### Query-Parameters
<a name="[id]"></a>
##### query

### Request
<a name="[id]"></a>
##### item
* Required: `["id"]`
* Properties:
  * `id`:
    * Type: integer
  * `userId`:
    * Type: integer
  * `title`:
    * Type: string
    * Pattern: `[A-z]+`
    * Min-Length: 3
    * Max-Length: 16
  * `date`:
    * Type: string
    * Format: date-time


### Response - 200:
<a name="[id]"></a>
##### message
* Properties:
  * `success`:
    * Type: boolean
  * `message`:
    * Type: string


## DELETE 
### Query-Parameters
<a name="[id]"></a>
##### query

### Request
<a name="[id]"></a>
##### item
* Required: `["id"]`
* Properties:
  * `id`:
    * Type: integer
  * `userId`:
    * Type: integer
  * `title`:
    * Type: string
    * Pattern: `[A-z]+`
    * Min-Length: 3
    * Max-Length: 16
  * `date`:
    * Type: string
    * Format: date-time


### Response - 200:
<a name="[id]"></a>
##### message
* Properties:
  * `success`:
    * Type: boolean
  * `message`:
    * Type: string


## PATCH 
### Query-Parameters
<a name="[id]"></a>
##### query

### Request
<a name="[id]"></a>
##### item
* Required: `["id"]`
* Properties:
  * `id`:
    * Type: integer
  * `userId`:
    * Type: integer
  * `title`:
    * Type: string
    * Pattern: `[A-z]+`
    * Min-Length: 3
    * Max-Length: 16
  * `date`:
    * Type: string
    * Format: date-time


### Response - 200:
<a name="[id]"></a>
##### message
* Properties:
  * `success`:
    * Type: boolean
  * `message`:
    * Type: string






