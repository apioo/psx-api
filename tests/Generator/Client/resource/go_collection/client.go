
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

// Endpoint: /foo
func (client Client) getFoo() FooResource {
    r := FooResource {
        BaseUrl: client.BaseUrl,
        Token: client.Token
    }
    return r
}
// Endpoint: /bar/:foo
func (client Client) getBarByFoo(string foo) BarByFooResource {
    r := BarByFooResource {
        Foo: foo,
        BaseUrl: client.BaseUrl,
        Token: client.Token
    }
    return r
}
// Endpoint: /bar/$year<[0-9]+>
func (client Client) getBarByYear(string year) BarByYearResource {
    r := BarByYearResource {
        Year: year,
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
