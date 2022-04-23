/**
 * Client generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import {ClientAbstract, CredentialsInterface, TokenStoreInterface} from "sdkgen-client"
import {HttpBearer} from "sdkgen-client"

import FooGroup from "./FooGroup";
import BarGroup from "./BarGroup";

export default class Client extends ClientAbstract {
    public constructor(baseUrl: string, token: string, tokenStore: TokenStoreInterface|null = null, scopes: Array<string>|null = []) {
        super(baseUrl, new HttpBearer(token), tokenStore, scopes);
    }

    /**
     * Tag: foo
     */
    public async foo(): Promise<FooGroup>
    {
        return new FooGroup(
            this.baseUrl,
            await this.newHttpClient()
        );
    }

    /**
     * Tag: bar
     */
    public async bar(): Promise<BarGroup>
    {
        return new BarGroup(
            this.baseUrl,
            await this.newHttpClient()
        );
    }

}
