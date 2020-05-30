/**
 * BarFooResource generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

import Axios, {AxiosInstance, AxiosPromise} from "axios";


export default class BarFooResource {
    private url: string;
    private token: string;
    private httpClient: AxiosInstance;

    private foo: string;

    public constructor(foo: string, baseUrl: string, token: string, httpClient?: AxiosInstance) {
        this.foo = foo;

        this.url = baseUrl + "/bar/"+foo+"";
        this.token = token;
        this.httpClient = httpClient ? httpClient : Axios.create();
    }

    /**
     * Returns a collection
     *
     * @returns {AxiosPromise<Collection>}
     */
    public get(): AxiosPromise<Collection> {
        let params = {
            method: "GET",
        };

        return this.httpClient.get<Collection>(this.url, params);
    }

    /**
     * @param {ItemCreate} data
     * @returns {AxiosPromise<Message>}
     */
    public post(data: ItemCreate): AxiosPromise<Message> {
        let params = {
            method: "POST",
        };

        return this.httpClient.post<Message>(this.url, data, params);
    }

}

