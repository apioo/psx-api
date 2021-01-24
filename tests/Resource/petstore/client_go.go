
// PetsResource generated on 0000-00-00
// {@link https://github.com/apioo}



import (
    "encoding/json"
    "io/ioutil"
    "net/http"
    "time"
)

type PetsResource struct {
    BaseUrl string
    Token string
}

// Listpets List all pets
func (r PetsResource) Listpets(query PetsGetQuery) Pets {


    req, err := http.NewRequest("GET", r.BaseURL + url, nil)

    client := &http.Client{}
    resp, err := client.Do(req)

    if err != nil {
        panic(err)
    }

    defer resp.Body.Close()
    respBody, _ := ioutil.ReadAll(resp.Body)

    var response Pets
    json.Unmarshal(respBody, &response)

    return response
}

// Createpets Create a pet
func (r PetsResource) Createpets(data Pet)  {

    raw, err := json.Marshal(data)
    if err != nil {
        panic(err)
    }
    var reqBody = bytes.NewReader(raw)

    req, err := http.NewRequest("POST", r.BaseURL + url, reqBody)
    req.Header.Set("Content-Type", "application/json")

    client := &http.Client{}
    resp, err := client.Do(req)

    if err != nil {
        panic(err)
    }

    defer resp.Body.Close()
    respBody, _ := ioutil.ReadAll(resp.Body)

    var response 
    json.Unmarshal(respBody, &response)

    return response
}


func NewPetsResource(baseUrl string, token string) PetsResource {
    r := PetsResource {
        BaseUrl: baseUrl + "/pets",
        Token: token
    }
    return r
}


// Pet generated on 0000-00-00
// {@link https://github.com/apioo}




// Pet
type Pet struct {
    Id int64 `json:"id"`
    Name string `json:"name"`
    Tag string `json:"tag"`
}


// Pets generated on 0000-00-00
// {@link https://github.com/apioo}




// Pets
type Pets struct {
    Pets []Pet `json:"pets"`
}


// Error generated on 0000-00-00
// {@link https://github.com/apioo}




// Error
type Error struct {
    Code int32 `json:"code"`
    Message string `json:"message"`
}


// PetsGetQuery generated on 0000-00-00
// {@link https://github.com/apioo}




// PetsGetQuery
type PetsGetQuery struct {
    Limit int32 `json:"limit"`
}


// PetsPetIdGetQuery generated on 0000-00-00
// {@link https://github.com/apioo}




// PetsPetIdGetQuery
type PetsPetIdGetQuery struct {
    PetId string `json:"petId"`
}


// Client generated on 0000-00-00
// {@link https://github.com/apioo}



import (
    "encoding/json"
    "io/ioutil"
    "net/http"
    "time"
)

type Client struct {
    BaseUrl string
    Token   string
}

// Endpoint: /pets
func (client Client) getPets() PetsResource {
    r := PetsResource {
        BaseUrl: client.BaseUrl,
        Token: client.Token
    }
    return r
}

func NewClient(baseUrl string, token string) Client {
    c := Client {
        BaseUrl: baseUrl,
        Token: token
    }
    return c
}
