
# /foo/:name/:type

lorem ipsum

### Path-Parameters

#### path

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
name | String | Name parameter | MaxLength: 16, Pattern: [A-z]+
type | String |  | 


## GET

Returns a collection

### GET Query-Parameters

#### query

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
startIndex | Integer | startIndex parameter | Maximum: 32
float | Number |  | 
boolean | Boolean |  | 
date | [Date](http://tools.ietf.org/html/rfc3339#section-5.6) |  | 
datetime | [DateTime](http://tools.ietf.org/html/rfc3339#section-5.6) |  | 


### GET Response - 200 OK

#### collection

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
entry | Array (Object ([item](#psx_model_Item))) |  | 

#### item

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
id | Integer |  | 
userId | Integer |  | 
title | String |  | MinLength: 3, MaxLength: 16, Pattern: [A-z]+
date | [DateTime](http://tools.ietf.org/html/rfc3339#section-5.6) |  | 


## POST


### POST Request

#### item

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
id | Integer |  | 
userId | Integer |  | 
title | String |  | MinLength: 3, MaxLength: 16, Pattern: [A-z]+
date | [DateTime](http://tools.ietf.org/html/rfc3339#section-5.6) |  | 


### POST Response - 201 Created

#### message

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
success | Boolean |  | 
message | String |  | 


## PUT


### PUT Request

#### item

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
id | Integer |  | 
userId | Integer |  | 
title | String |  | MinLength: 3, MaxLength: 16, Pattern: [A-z]+
date | [DateTime](http://tools.ietf.org/html/rfc3339#section-5.6) |  | 


### PUT Response - 200 OK

#### message

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
success | Boolean |  | 
message | String |  | 


## DELETE


### DELETE Request

#### item

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
id | Integer |  | 
userId | Integer |  | 
title | String |  | MinLength: 3, MaxLength: 16, Pattern: [A-z]+
date | [DateTime](http://tools.ietf.org/html/rfc3339#section-5.6) |  | 


### DELETE Response - 200 OK

#### message

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
success | Boolean |  | 
message | String |  | 


## PATCH


### PATCH Request

#### item

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
id | Integer |  | 
userId | Integer |  | 
title | String |  | MinLength: 3, MaxLength: 16, Pattern: [A-z]+
date | [DateTime](http://tools.ietf.org/html/rfc3339#section-5.6) |  | 


### PATCH Response - 200 OK

#### message

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
success | Boolean |  | 
message | String |  | 

