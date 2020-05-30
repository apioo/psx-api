
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

// Listfoo Returns a collection
func Listfoo(query GetQuery) Collection {


    req, err := http.NewRequest("GET", baseURL + url, nil)

    client := &http.Client{}
    resp, err := client.Do(req)

    if err != nil {
        panic(err)
    }

    defer resp.Body.Close()
    respBody, _ := ioutil.ReadAll(resp.Body)

    var response Collection
    json.Unmarshal(respBody, &response)

    return response
}

// Createfoo 
func Createfoo(data ItemCreate) Message {

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

    var response Message
    json.Unmarshal(respBody, &response)

    return response
}

// Put 
func Put(data ItemUpdate) Message {

    raw, err := json.Marshal(data)
    if err != nil {
        panic(err)
    }
    var reqBody = bytes.NewReader(raw)

    req, err := http.NewRequest("PUT", baseURL + url, reqBody)
    req.Header.Set("Content-Type", "application/json")

    client := &http.Client{}
    resp, err := client.Do(req)

    if err != nil {
        panic(err)
    }

    defer resp.Body.Close()
    respBody, _ := ioutil.ReadAll(resp.Body)

    var response Message
    json.Unmarshal(respBody, &response)

    return response
}

// Delete 
func Delete() Message {


    req, err := http.NewRequest("DELETE", baseURL + url, nil)

    client := &http.Client{}
    resp, err := client.Do(req)

    if err != nil {
        panic(err)
    }

    defer resp.Body.Close()
    respBody, _ := ioutil.ReadAll(resp.Body)

    var response Message
    json.Unmarshal(respBody, &response)

    return response
}

// Patch 
func Patch(data ItemPatch) Message {

    raw, err := json.Marshal(data)
    if err != nil {
        panic(err)
    }
    var reqBody = bytes.NewReader(raw)

    req, err := http.NewRequest("PATCH", baseURL + url, reqBody)
    req.Header.Set("Content-Type", "application/json")

    client := &http.Client{}
    resp, err := client.Do(req)

    if err != nil {
        panic(err)
    }

    defer resp.Body.Close()
    respBody, _ := ioutil.ReadAll(resp.Body)

    var response Message
    json.Unmarshal(respBody, &response)

    return response
}

