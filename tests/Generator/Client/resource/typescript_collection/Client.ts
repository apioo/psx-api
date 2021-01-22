/**
 * Client generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

import Axios, {AxiosInstance, AxiosPromise} from "axios";
import FooResource from "./FooResource";
import BarFooResource from "./BarFooResource";
import BarYear09Resource from "./BarYear09Resource";


export default class  {
    private baseUrl: string;
    private token: string;
    private httpClient: AxiosInstance;

    public constructor(baseUrl: string, token: string, httpClient?: AxiosInstance) {
        this.baseUrl = baseUrl;
        this.token = token;
        this.httpClient = httpClient ? httpClient : Axios.create();
    }

    /**
     * Endpoint: /foo
     * 
     * @returns FooResource
     */
    public getFoo(): FooResource
    {
        return new FooResource(
            this.baseUrl,
            this.token,
            this.httpClient
        );
    }

    /**
     * Endpoint: /bar/:foo
     * 
     * @returns BarFooResource
     */
    public getBarFoo(foo: string): BarFooResource
    {
        return new BarFooResource(
            foo,
            this.baseUrl,
            this.token,
            this.httpClient
        );
    }

    /**
     * Endpoint: /bar/$year<[0-9]+>
     * 
     * @returns BarYear09Resource
     */
    public getBarYear09(year: string): BarYear09Resource
    {
        return new BarYear09Resource(
            year,
            this.baseUrl,
            this.token,
            this.httpClient
        );
    }

}

