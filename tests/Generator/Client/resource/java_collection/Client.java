/**
 * Client automatically generated by SDKgen please do not edit this file manually
 * @see https://sdkgen.app
 */


import app.sdkgen.client.ClientAbstract;
import app.sdkgen.client.Credentials.*;
import app.sdkgen.client.CredentialsInterface;
import app.sdkgen.client.TokenStoreInterface;
import java.util.List;

public class Client extends ClientAbstract {
    public Client(String baseUrl, String token, TokenStoreInterface tokenStore, List<String> scopes) {
        super(baseUrl, new HttpBearer(token), tokenStore, scopes);
    }

    /**
     * Tag: foo
     */
    public FooGroup foo() {
        return new FooGroup(
            this.baseUrl,
            this.newHttpClient(),
            this.objectMapper
        );
    }

    /**
     * Tag: bar
     */
    public BarGroup bar() {
        return new BarGroup(
            this.baseUrl,
            this.newHttpClient(),
            this.objectMapper
        );
    }

}
