/**
 * FooNameTypeResource generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

import Axios, {AxiosInstance, AxiosPromise} from "axios";


export default class FooNameTypeResource {
    private url: string;
    private token: string;
    private httpClient: AxiosInstance;

    private name: string;
    private type: string;

    public constructor(name: string, type: string, baseUrl: string, token: string, httpClient?: AxiosInstance) {
        this.name = name;
        this.type = type;

        this.url = baseUrl + "/foo/"+name+"/"+type+"";
        this.token = token;
        this.httpClient = httpClient ? httpClient : Axios.create();
    }

    /**
     * Returns a collection
     *
     * @param {GetQuery} query
     * @returns {AxiosPromise<EntryCollection>}
     */
    public listFoo(query: GetQuery): AxiosPromise<EntryCollection> {
        let params = {
            method: "GET",
            params: query,
        };

        return this.httpClient.get<EntryCollection>(this.url, params);
    }

    /**
     * @param {EntryCreate} data
     * @returns {AxiosPromise<EntryMessage>}
     */
    public createFoo(data: EntryCreate): AxiosPromise<EntryMessage> {
        let params = {
            method: "POST",
        };

        return this.httpClient.post<EntryMessage>(this.url, data, params);
    }

    /**
     * @param {EntryUpdate} data
     * @returns {AxiosPromise<EntryMessage>}
     */
    public put(data: EntryUpdate): AxiosPromise<EntryMessage> {
        let params = {
            method: "PUT",
        };

        return this.httpClient.put<EntryMessage>(this.url, data, params);
    }

    /**
     * @returns {AxiosPromise<EntryMessage>}
     */
    public delete(): AxiosPromise<EntryMessage> {
        let params = {
            method: "DELETE",
        };

        return this.httpClient.delete(this.url, params);
    }

    /**
     * @param {EntryPatch} data
     * @returns {AxiosPromise<EntryMessage>}
     */
    public patch(data: EntryPatch): AxiosPromise<EntryMessage> {
        let params = {
            method: "PATCH",
        };

        return this.httpClient.patch<EntryMessage>(this.url, data, params);
    }

}

