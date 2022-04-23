/**
 * Client generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import {ClientAbstract, CredentialsInterface, TokenStoreInterface} from "sdkgen-client"
import {HttpBearer} from "sdkgen-client"

import FooByNameAndTypeResource from "./FooByNameAndTypeResource";

export default class Client extends ClientAbstract {
    public constructor(baseUrl: string, token: string, tokenStore: TokenStoreInterface|null = null, scopes: Array<string>|null = []) {
        super(baseUrl, new HttpBearer(token), tokenStore, scopes);
    }

    /**
     * Endpoint: /foo/:name/:type
     */
    public async getFooByNameAndType(name: string, type: string): Promise<FooByNameAndTypeResource>
    {
        return new FooByNameAndTypeResource(
            name,
            type,
            this.baseUrl,
            await this.newHttpClient()
        );
    }

}
