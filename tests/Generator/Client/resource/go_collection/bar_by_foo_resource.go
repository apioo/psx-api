
// BarByFooResource generated on 0000-00-00
// {@link https://github.com/apioo}



import (
    "encoding/json"
    "io/ioutil"
    "net/http"
    "time"
)

type BarByFooResource struct {
    BaseUrl string
    Token string
    Foo string
}

// Get Returns a collection
func (r BarByFooResource) Get() EntryCollection {


    req, err := http.NewRequest("GET", r.BaseURL + url, nil)

    client := &http.Client{}
    resp, err := client.Do(req)

    if err != nil {
        panic(err)
    }

    defer resp.Body.Close()
    respBody, _ := ioutil.ReadAll(resp.Body)

    var response EntryCollection
    json.Unmarshal(respBody, &response)

    return response
}

// Post 
func (r BarByFooResource) Post(data EntryCreate) EntryMessage {

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

    var response EntryMessage
    json.Unmarshal(respBody, &response)

    return response
}


func NewBarByFooResource(foo string, baseUrl string, token string) BarByFooResource {
    r := BarByFooResource {
        BaseUrl: baseUrl + "/bar/"+foo+"",
        Token: token
    }
    return r
}
