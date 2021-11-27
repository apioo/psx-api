/**
 * Client generated on 0000-00-00
 * @see https://sdkgen.app
 */


import app.sdkgen.client.ClientAbstract;
import org.apache.http.client.HttpClient;
import org.apache.http.impl.client.HttpClientBuilder;

public class Client extends ClientAbstract
{
    public function Client(String baseUrl, String token, TokenStoreInterface tokenStore)
    {
        super(baseUrl, new HttpBearer(token), tokenStore);
    }

    public function Client(String baseUrl, String token, )
    {
        super(baseUrl, new HttpBearer(token), null);
    }

    /**
     * Tag: foo
     */
    public FooGroup foo()
    {
        return new FooGroup(
            this.baseUrl,
            this.newHttpClient(),
            this.httpClient
        );
    }

    /**
     * Tag: bar
     */
    public BarGroup bar()
    {
        return new BarGroup(
            this.baseUrl,
            this.newHttpClient(),
            this.httpClient
        );
    }

}
