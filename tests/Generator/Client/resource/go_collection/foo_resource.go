
// FooResource generated on 0000-00-00
// {@link https://github.com/apioo}



package fooresource

import (
    "encoding/json"
    "io/ioutil"
    "net/http"
    "time"
)

var baseURL = ""



var url = "/foo"

// SetBaseURL sets the base url
func SetBaseURL(url string) {
    baseURL = url
}

// Get Returns a collection
func Get() EntryCollection {


    req, err := http.NewRequest("GET", baseURL + url, nil)

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
func Post(data EntryCreate) EntryMessage {

    raw, err := json.Marshal(data)
    if err != nil {
        panic(err)
    }
    var reqBody = bytes.NewReader(raw)

    req, err := http.NewRequest("POST", baseURL + url, reqBody)
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

