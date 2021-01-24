/**
 * Client generated on 0000-00-00
 * {@link https://github.com/apioo}
 */


import org.apache.http.client.HttpClient;
import org.apache.http.impl.client.HttpClientBuilder;

public class Client
{
    private final String baseUrl;
    private final String token;
    private final HttpClient httpClient;

    public Client(String baseUrl, String token, HttpClient httpClient)
    {
        this.baseUrl = baseUrl;
        this.token = token;
        this.httpClient = httpClient != null ? httpClient : HttpClientBuilder.create().build();
    }

    /**
     * Endpoint: /foo
     */
    public FooResource getFoo()
    {
        return new FooResource(
            this.baseUrl,
            this.token,
            this.httpClient
        );
    }

    /**
     * Endpoint: /bar/:foo
     */
    public BarByFooResource getBarByFoo(String foo)
    {
        return new BarByFooResource(
            foo,
            this.baseUrl,
            this.token,
            this.httpClient
        );
    }

    /**
     * Endpoint: /bar/$year<[0-9]+>
     */
    public BarByYearResource getBarByYear(String year)
    {
        return new BarByYearResource(
            year,
            this.baseUrl,
            this.token,
            this.httpClient
        );
    }

}
