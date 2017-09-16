
# /pets

foobar

## GET

List all pets

### GET Query-Parameters

#### query

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
limit | Integer |  | 


### GET Response - 200 OK

#### Pets

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
pets | Array (Object ([Pet](#psx_model_Pet))) |  | 

#### Pet

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
id | Integer |  | 
name | String |  | 
tag | String |  | 


### GET Response - 500 Internal Server Error

#### Error

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
code | Integer |  | 
message | String |  | 


## POST

Create a pet

### POST Request

#### Pet

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
id | Integer |  | 
name | String |  | 
tag | String |  | 


### POST Response - 500 Internal Server Error

#### Error

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
code | Integer |  | 
message | String |  | 

