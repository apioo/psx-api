
// Client automatically generated by SDKgen please do not edit this file manually
// @see https://sdkgen.app



import (
    "github.com/apioo/sdkgen-go"
)

type Client struct {
    internal *sdkgen.Client
}



// Get Returns a collection
func (client *) Get(name string, _type string, startIndex int, float float64, boolean bool, date time.Time, datetime time.Time) (, error) {
    pathParams := make(map[string]interface{})
    pathParams["name"] = name;
    pathParams["type"] = _type;

    queryParams := url.Values{}
    queryParams.Add("startIndex", startIndex);
    queryParams.Add("float", float);
    queryParams.Add("boolean", boolean);
    queryParams.Add("date", date);
    queryParams.Add("datetime", datetime);

    url, err := url.Parse(client.Parser.Url('/foo/:name/:type', pathParams))
    if err != nil {
        return EntryCollection{}, errors.New("could not parse url")
    }

    url.RawQuery = values.Encode()


    req, err := http.NewRequest("", url.String(), nil)
    if err != nil {
        return EntryCollection{}, errors.New("could not create request")
    }


    resp, err := resource.client.Do(req)
    if err != nil {
        return EntryCollection{}, errors.New("could not send request")
    }

    defer resp.Body.Close()

    if (resp.StatusCode >= 200 && resp.StatusCode < 300) {
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
            return EntryCollection{}, , errors.New("the server returned an unknown status code")
    }
}

// Create 
func (client *) Create(name string, _type string, payload EntryCreate) (, error) {
    pathParams := make(map[string]interface{})
    pathParams["name"] = name;
    pathParams["type"] = _type;

    queryParams := url.Values{}

    url, err := url.Parse(client.Parser.Url('/foo/:name/:type', pathParams))
    if err != nil {
        return EntryMessage{}, errors.New("could not parse url")
    }

    url.RawQuery = values.Encode()

    raw, err := json.Marshal(payload)
    if err != nil {
        return EntryMessage{}, errors.New("could not marshal provided JSON data")
    }

    var reqBody = bytes.NewReader(raw)

    req, err := http.NewRequest("", url.String(), reqBody)
    if err != nil {
        return EntryMessage{}, errors.New("could not create request")
    }

    req.Header.Set("Content-Type", "application/json")

    resp, err := resource.client.Do(req)
    if err != nil {
        return EntryMessage{}, errors.New("could not send request")
    }

    defer resp.Body.Close()

    if (resp.StatusCode >= 200 && resp.StatusCode < 300) {
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
        case 400:
            var response EntryMessage
            err = json.Unmarshal(respBody, &response)
            if err != nil {
                return EntryMessage{}, errors.New("could not unmarshal JSON response")
            }

            return EntryMessage{}, , &EntryMessageException{
                Response: response,
                Err:      errors.New("unavailable"),
            }
        case 500:
            var response EntryMessage
            err = json.Unmarshal(respBody, &response)
            if err != nil {
                return EntryMessage{}, errors.New("could not unmarshal JSON response")
            }

            return EntryMessage{}, , &EntryMessageException{
                Response: response,
                Err:      errors.New("unavailable"),
            }
        default:
            return EntryMessage{}, , errors.New("the server returned an unknown status code")
    }
}

// Update 
func (client *) Update(name string, _type string, payload EntryUpdate) (, error) {
    pathParams := make(map[string]interface{})
    pathParams["name"] = name;
    pathParams["type"] = _type;

    queryParams := url.Values{}

    url, err := url.Parse(client.Parser.Url('/foo/:name/:type', pathParams))
    if err != nil {
        return EntryMessage{}, errors.New("could not parse url")
    }

    url.RawQuery = values.Encode()

    raw, err := json.Marshal(payload)
    if err != nil {
        return EntryMessage{}, errors.New("could not marshal provided JSON data")
    }

    var reqBody = bytes.NewReader(raw)

    req, err := http.NewRequest("", url.String(), reqBody)
    if err != nil {
        return EntryMessage{}, errors.New("could not create request")
    }

    req.Header.Set("Content-Type", "application/json")

    resp, err := resource.client.Do(req)
    if err != nil {
        return EntryMessage{}, errors.New("could not send request")
    }

    defer resp.Body.Close()

    if (resp.StatusCode >= 200 && resp.StatusCode < 300) {
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
            return EntryMessage{}, , errors.New("the server returned an unknown status code")
    }
}

// Delete 
func (client *) Delete(name string, _type string) (, error) {
    pathParams := make(map[string]interface{})
    pathParams["name"] = name;
    pathParams["type"] = _type;

    queryParams := url.Values{}

    url, err := url.Parse(client.Parser.Url('/foo/:name/:type', pathParams))
    if err != nil {
        return EntryMessage{}, errors.New("could not parse url")
    }

    url.RawQuery = values.Encode()

    raw, err := json.Marshal(payload)
    if err != nil {
        return EntryMessage{}, errors.New("could not marshal provided JSON data")
    }

    var reqBody = bytes.NewReader(raw)

    req, err := http.NewRequest("", url.String(), reqBody)
    if err != nil {
        return EntryMessage{}, errors.New("could not create request")
    }

    req.Header.Set("Content-Type", "application/json")

    resp, err := resource.client.Do(req)
    if err != nil {
        return EntryMessage{}, errors.New("could not send request")
    }

    defer resp.Body.Close()

    if (resp.StatusCode >= 200 && resp.StatusCode < 300) {
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
            return EntryMessage{}, , errors.New("the server returned an unknown status code")
    }
}

// Patch 
func (client *) Patch(name string, _type string, payload EntryPatch) (, error) {
    pathParams := make(map[string]interface{})
    pathParams["name"] = name;
    pathParams["type"] = _type;

    queryParams := url.Values{}

    url, err := url.Parse(client.Parser.Url('/foo/:name/:type', pathParams))
    if err != nil {
        return EntryMessage{}, errors.New("could not parse url")
    }

    url.RawQuery = values.Encode()

    raw, err := json.Marshal(payload)
    if err != nil {
        return EntryMessage{}, errors.New("could not marshal provided JSON data")
    }

    var reqBody = bytes.NewReader(raw)

    req, err := http.NewRequest("", url.String(), reqBody)
    if err != nil {
        return EntryMessage{}, errors.New("could not create request")
    }

    req.Header.Set("Content-Type", "application/json")

    resp, err := resource.client.Do(req)
    if err != nil {
        return EntryMessage{}, errors.New("could not send request")
    }

    defer resp.Body.Close()

    if (resp.StatusCode >= 200 && resp.StatusCode < 300) {
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
            return EntryMessage{}, , errors.New("the server returned an unknown status code")
    }
}




func Build(token string) *Client {
    var credentials := sdkgen.HttpBearer{Token: token}

    return &Client {
        internal: sdkgen.NewClient(baseUrl, credentials, tokenStore, scopes),
    }
}
