/**
 * FooByNameAndTypeResource generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import Axios, {AxiosInstance, AxiosPromise, Method} from "axios";
import {GetQuery} from "./GetQuery";
import {EntryCollection} from "./EntryCollection";
import {EntryCreate} from "./EntryCreate";
import {EntryMessage} from "./EntryMessage";
import {EntryUpdate} from "./EntryUpdate";
import {EntryPatch} from "./EntryPatch";

export default class FooByNameAndTypeResource extends ResourceAbstract {
    private url: string;

    private name: string;
    private type: string;

    public constructor(name: string, type: string, baseUrl: string, httpClient?: AxiosInstance) {
        super(baseUrl, httpClient);

        this.name = name;
        this.type = type;

        this.url = baseUrl + "/foo/"+name+"/"+type+"";
    }

    /**
     * Returns a collection
     *
     * @param {GetQuery} query
     * @returns {AxiosPromise<EntryCollection>}
     */
    public listFoo(query?: GetQuery): AxiosPromise<EntryCollection> {
        let params = {
            method: <Method> "GET",
            params: query,
        };

        return this.httpClient.get<EntryCollection>(this.url, params);
    }

    /**
     * @param {EntryCreate} data
     * @returns {AxiosPromise<EntryMessage>}
     */
    public createFoo(data?: EntryCreate): AxiosPromise<EntryMessage> {
        let params = {
            method: <Method> "POST",
        };

        return this.httpClient.post<EntryMessage>(this.url, data, params);
    }

    /**
     * @param {EntryUpdate} data
     * @returns {AxiosPromise<EntryMessage>}
     */
    public put(data?: EntryUpdate): AxiosPromise<EntryMessage> {
        let params = {
            method: <Method> "PUT",
        };

        return this.httpClient.put<EntryMessage>(this.url, data, params);
    }

    /**
     * @returns {AxiosPromise<EntryMessage>}
     */
    public delete(): AxiosPromise<EntryMessage> {
        let params = {
            method: <Method> "DELETE",
        };

        return this.httpClient.delete(this.url, params);
    }

    /**
     * @param {EntryPatch} data
     * @returns {AxiosPromise<EntryMessage>}
     */
    public patch(data?: EntryPatch): AxiosPromise<EntryMessage> {
        let params = {
            method: <Method> "PATCH",
        };

        return this.httpClient.patch<EntryMessage>(this.url, data, params);
    }

}
