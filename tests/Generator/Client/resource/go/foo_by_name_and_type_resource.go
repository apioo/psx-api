
// FooByNameAndTypeResource automatically generated by SDKgen please do not edit this file manually
// @see https://sdkgen.app



import (
    "bytes"
    "encoding/json"
    "errors"
    "io/ioutil"
    "net/http"
    "github.com/apioo/sdkgen-go"
)

type FooByNameAndTypeResource struct {
    url string
    client *http.Client
}

// listFoo Returns a collection
func (resource FooByNameAndTypeResource) listFoo(query GetQuery) (EntryCollection, error) {
    url, err := url.Parse(resource.url)
    if err != nil {
        return EntryCollection{}, errors.New("could not parse url")
    }

    rawJson, err := json.Marshal(query)
    if err != nil {
        return EntryCollection{}, errors.New("could not marshall query")
    }

    parameters := url.Query()
    err = json.Unmarshal(rawJson, &parameters)
    url.RawQuery = parameters.Encode()


    req, err := http.NewRequest("GET", url.String(), nil)
    if err != nil {
        return EntryCollection{}, errors.New("could not create request")
    }


    resp, err := resource.client.Do(req)
    if err != nil {
        return EntryCollection{}, errors.New("could not send request")
    }

    defer resp.Body.Close()

    respBody, err := ioutil.ReadAll(resp.Body)
    if err != nil {
        return EntryCollection{}, errors.New("could not read response body")
    }

    var response EntryCollection

    err = json.Unmarshal(respBody, &response)
    if err != nil {
        return EntryCollection{}, errors.New("could not unmarshal JSON response")
    }

    return response, nil
}

// createFoo 
func (resource FooByNameAndTypeResource) createFoo(data EntryCreate) (EntryMessage, error) {
    url, err := url.Parse(resource.url)
    if err != nil {
        return EntryMessage{}, errors.New("could not parse url")
    }


    raw, err := json.Marshal(data)
    if err != nil {
        return EntryMessage{}, errors.New("could not marshal provided JSON data")
    }

    var reqBody = bytes.NewReader(raw)

    req, err := http.NewRequest("POST", url.String(), reqBody)
    if err != nil {
        return EntryMessage{}, errors.New("could not create request")
    }

    req.Header.Set("Content-Type", "application/json")

    resp, err := resource.client.Do(req)
    if err != nil {
        return EntryMessage{}, errors.New("could not send request")
    }

    defer resp.Body.Close()

    respBody, err := ioutil.ReadAll(resp.Body)
    if err != nil {
        return EntryMessage{}, errors.New("could not read response body")
    }

    var response EntryMessage

    err = json.Unmarshal(respBody, &response)
    if err != nil {
        return EntryMessage{}, errors.New("could not unmarshal JSON response")
    }

    return response, nil
}

// put 
func (resource FooByNameAndTypeResource) put(data EntryUpdate) (EntryMessage, error) {
    url, err := url.Parse(resource.url)
    if err != nil {
        return EntryMessage{}, errors.New("could not parse url")
    }


    raw, err := json.Marshal(data)
    if err != nil {
        return EntryMessage{}, errors.New("could not marshal provided JSON data")
    }

    var reqBody = bytes.NewReader(raw)

    req, err := http.NewRequest("PUT", url.String(), reqBody)
    if err != nil {
        return EntryMessage{}, errors.New("could not create request")
    }

    req.Header.Set("Content-Type", "application/json")

    resp, err := resource.client.Do(req)
    if err != nil {
        return EntryMessage{}, errors.New("could not send request")
    }

    defer resp.Body.Close()

    respBody, err := ioutil.ReadAll(resp.Body)
    if err != nil {
        return EntryMessage{}, errors.New("could not read response body")
    }

    var response EntryMessage

    err = json.Unmarshal(respBody, &response)
    if err != nil {
        return EntryMessage{}, errors.New("could not unmarshal JSON response")
    }

    return response, nil
}

// delete 
func (resource FooByNameAndTypeResource) delete() (EntryMessage, error) {
    url, err := url.Parse(resource.url)
    if err != nil {
        return EntryMessage{}, errors.New("could not parse url")
    }



    req, err := http.NewRequest("DELETE", url.String(), nil)
    if err != nil {
        return EntryMessage{}, errors.New("could not create request")
    }


    resp, err := resource.client.Do(req)
    if err != nil {
        return EntryMessage{}, errors.New("could not send request")
    }

    defer resp.Body.Close()

    respBody, err := ioutil.ReadAll(resp.Body)
    if err != nil {
        return EntryMessage{}, errors.New("could not read response body")
    }

    var response EntryMessage

    err = json.Unmarshal(respBody, &response)
    if err != nil {
        return EntryMessage{}, errors.New("could not unmarshal JSON response")
    }

    return response, nil
}

// patch 
func (resource FooByNameAndTypeResource) patch(data EntryPatch) (EntryMessage, error) {
    url, err := url.Parse(resource.url)
    if err != nil {
        return EntryMessage{}, errors.New("could not parse url")
    }


    raw, err := json.Marshal(data)
    if err != nil {
        return EntryMessage{}, errors.New("could not marshal provided JSON data")
    }

    var reqBody = bytes.NewReader(raw)

    req, err := http.NewRequest("PATCH", url.String(), reqBody)
    if err != nil {
        return EntryMessage{}, errors.New("could not create request")
    }

    req.Header.Set("Content-Type", "application/json")

    resp, err := resource.client.Do(req)
    if err != nil {
        return EntryMessage{}, errors.New("could not send request")
    }

    defer resp.Body.Close()

    respBody, err := ioutil.ReadAll(resp.Body)
    if err != nil {
        return EntryMessage{}, errors.New("could not read response body")
    }

    var response EntryMessage

    err = json.Unmarshal(respBody, &response)
    if err != nil {
        return EntryMessage{}, errors.New("could not unmarshal JSON response")
    }

    return response, nil
}


func NewFooByNameAndTypeResource(name string, _type string, resource *sdkgen.Resource) *FooByNameAndTypeResource {
    return &FooByNameAndTypeResource {
        url: resource.BaseUrl + "/foo/"+name+"/"+_type+"",
        client: resource.HttpClient,
    }
}
