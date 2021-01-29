/**
 * Client generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

import Axios, {AxiosInstance, AxiosPromise} from "axios";
import FooByNameAndTypeResource from "./FooByNameAndTypeResource";

export default class Client {
    private baseUrl: string;
    private token: string;
    private httpClient: AxiosInstance;

    public constructor(baseUrl: string, token: string, httpClient?: AxiosInstance) {
        this.baseUrl = baseUrl;
        this.token = token;
        this.httpClient = httpClient ? httpClient : Axios.create();
    }

    /**
     * Endpoint: /foo/:name/:type
     * 
     * @returns FooByNameAndTypeResource
     */
    public getFooByNameAndType(name: string, type: string): FooByNameAndTypeResource
    {
        return new FooByNameAndTypeResource(
            name,
            type,
            this.baseUrl,
            this.token,
            this.httpClient
        );
    }

}
