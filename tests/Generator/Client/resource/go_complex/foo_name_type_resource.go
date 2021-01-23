
// FooNameTypeResource generated on 0000-00-00
// {@link https://github.com/apioo}



import (
    "encoding/json"
    "io/ioutil"
    "net/http"
    "time"
)

type FooNameTypeResource struct {
    BaseUrl string
    Token string
    Name string
    Type string
}

// Postentryormessage Returns a collection
func (r FooNameTypeResource) Postentryormessage(data interface{}) interface{} {

    raw, err := json.Marshal(data)
    if err != nil {
        panic(err)
    }
    var reqBody = bytes.NewReader(raw)

    req, err := http.NewRequest("POST", r.BaseURL + url, reqBody)
    req.Header.Set("Content-Type", "application/json")
    req.Header.Set("Authorization", "Bearer " + r.Token)

    client := &http.Client{}
    resp, err := client.Do(req)

    if err != nil {
        panic(err)
    }

    defer resp.Body.Close()
    respBody, _ := ioutil.ReadAll(resp.Body)

    var response interface{}
    json.Unmarshal(respBody, &response)

    return response
}


func NewFooNameTypeResource(name string, type string, baseUrl string, token string) FooNameTypeResource {
    r := FooNameTypeResource {
        BaseUrl: baseUrl + "/foo/"+name+"/"+type+"",
        Token: token
    }
    return r
}
