
// FooNameTypeResource generated on 0000-00-00
// {@link https://github.com/apioo}



package foonametyperesource

import (
    "encoding/json"
    "io/ioutil"
    "net/http"
    "time"
)

var baseURL = ""

var name = "";
var type = "";


var url = "/foo/"+name+"/"+type+""

// SetBaseURL sets the base url
func SetBaseURL(url string) {
    baseURL = url
}

// Postentryormessage Returns a collection
func Postentryormessage(data interface{}) interface{} {

    raw, err := json.Marshal(data)
    if err != nil {
        panic(err)
    }
    var reqBody = bytes.NewReader(raw)

    req, err := http.NewRequest("POST", baseURL + url, reqBody)
    req.Header.Set("Content-Type", "application/json")
    req.Header.Set("Authorization", "Bearer " + token)

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

