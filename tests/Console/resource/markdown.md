
# /foo

Test description

### Path-Parameters

#### path

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
fooId | String |  | 


## GET

A long **Test** description

### GET Query-Parameters

#### query

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
foo | String | Test | 
bar | String |  | 
baz | String |  | 
boz | String |  | Pattern: [A-z]+
integer | Integer |  | 
number | Number |  | 
date | String |  | 
boolean | Boolean |  | 
string | String |  | 


### GET Request

#### Song

A canonical song

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
title | String |  | 
artist | String |  | 
length | Integer |  | 
ratings | Array (Object ([Rating](#psx_model_Rating))) |  | 

#### Rating

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
author | String |  | 
rating | Integer |  | 
text | String |  | 


### GET Response - 200 OK

#### Song

A canonical song

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
title | String |  | 
artist | String |  | 
length | Integer |  | 
ratings | Array (Object ([Rating](#psx_model_Rating))) |  | 

#### Rating

Field | Type | Description | Constraints
----- | ---- | ----------- | -----------
author | String |  | 
rating | Integer |  | 
text | String |  | 

