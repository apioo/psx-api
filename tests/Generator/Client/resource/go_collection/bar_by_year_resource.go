
// BarByYearResource automatically generated by SDKgen please do not edit this file manually
// @see https://sdkgen.app



import (
    "bytes"
    "encoding/json"
    "errors"
    "github.com/apioo/sdkgen-go"
    "io"
    "net/http"
    "net/url"
)

type BarByYearResource struct {
    url string
    client *http.Client
}

// Get Returns a collection
func (resource BarByYearResource) Get() (EntryCollection, error) {
    url, err := url.Parse(resource.url)
    if err != nil {
        return EntryCollection{}, errors.New("could not parse url")
    }



    req, err := http.NewRequest("GET", url.String(), nil)
    if err != nil {
        return EntryCollection{}, errors.New("could not create request")
    }


    resp, err := resource.client.Do(req)
    if err != nil {
        return EntryCollection{}, errors.New("could not send request")
    }

    defer resp.Body.Close()

    respBody, err := io.ReadAll(resp.Body)
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

// Post 
func (resource BarByYearResource) Post(data EntryCreate) (EntryMessage, error) {
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

    respBody, err := io.ReadAll(resp.Body)
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


func NewBarByYearResource(year string, resource *sdkgen.Resource) *BarByYearResource {
    return &BarByYearResource {
        url: resource.BaseUrl + "/bar/"+year+"",
        client: resource.HttpClient,
    }
}
