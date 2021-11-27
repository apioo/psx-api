/**
 * Client generated on 0000-00-00
 * @see https://sdkgen.app
 */


import app.sdkgen.client.ClientAbstract;
import org.apache.http.client.HttpClient;
import org.apache.http.impl.client.HttpClientBuilder;

public class Client extends ClientAbstract
{
    public function Client(String baseUrl, TokenStoreInterface tokenStore)
    {
        super(baseUrl, null, tokenStore);
    }

    public function Client(String baseUrl, )
    {
        super(baseUrl, null, null);
    }

    /**
     * Endpoint: /foo/:name/:type
     */
    public FooByNameAndTypeResource getFooByNameAndType(String name, String type)
    {
        return new FooByNameAndTypeResource(
            name,
            type,
            this.baseUrl,
            this.newHttpClient(),
            this.httpClient
        );
    }

}
