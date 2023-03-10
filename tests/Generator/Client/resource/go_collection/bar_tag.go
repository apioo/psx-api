
// BarTag automatically generated by SDKgen please do not edit this file manually
// @see https://sdkgen.app



import (
    "github.com/apioo/sdkgen-go"
)

type BarTag struct {
    internal *sdkgen.TagAbstract
}


// Get Returns a collection
func (client *Client) Get(foo string) (EntryCollection, error) {
    pathParams := make(map[string]interface{})
    pathParams["foo"] = foo

    queryParams := make(map[string]interface{})

    u, err := url.Parse(client.internal.Parser.Url("/bar/:foo", pathParams))
    if err != nil {
        return EntryCollection{}, errors.New("could not parse url")
    }

    u.RawQuery = client.internal.Parser.Query(queryParams).Encode()


    req, err := http.NewRequest("GET", u.String(), nil)
    if err != nil {
        return EntryCollection{}, errors.New("could not create request")
    }


    resp, err := client.internal.HttpClient.Do(req)
    if err != nil {
        return EntryCollection{}, errors.New("could not send request")
    }

    defer resp.Body.Close()

    if resp.StatusCode >= 200 && resp.StatusCode < 300 {
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

    switch resp.StatusCode {
        default:
            return EntryCollection{}, errors.New("the server returned an unknown status code")
    }
}

// Create 
func (client *Client) Create(payload EntryCreate) (EntryMessage, error) {
    pathParams := make(map[string]interface{})

    queryParams := make(map[string]interface{})

    u, err := url.Parse(client.internal.Parser.Url("/bar/:foo", pathParams))
    if err != nil {
        return EntryMessage{}, errors.New("could not parse url")
    }

    u.RawQuery = client.internal.Parser.Query(queryParams).Encode()

    raw, err := json.Marshal(payload)
    if err != nil {
        return EntryMessage{}, errors.New("could not marshal provided JSON data")
    }

    var reqBody = bytes.NewReader(raw)

    req, err := http.NewRequest("POST", u.String(), reqBody)
    if err != nil {
        return EntryMessage{}, errors.New("could not create request")
    }

    req.Header.Set("Content-Type", "application/json")

    resp, err := client.internal.HttpClient.Do(req)
    if err != nil {
        return EntryMessage{}, errors.New("could not send request")
    }

    defer resp.Body.Close()

    if resp.StatusCode >= 200 && resp.StatusCode < 300 {
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

    switch resp.StatusCode {
        default:
            return EntryMessage{}, errors.New("the server returned an unknown status code")
    }
}


