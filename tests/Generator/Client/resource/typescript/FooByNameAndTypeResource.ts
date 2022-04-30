/**
 * FooByNameAndTypeResource generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import {AxiosInstance, AxiosRequestConfig, AxiosResponse} from "axios";
import {ResourceAbstract} from "sdkgen-client"
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

    public constructor(name: string, type: string, baseUrl: string, httpClient: AxiosInstance) {
        super(baseUrl, httpClient);

        this.name = name;
        this.type = type;

        this.url = baseUrl + "/foo/"+name+"/"+type+"";
    }

    /**
     * Returns a collection
     *
     * @param {GetQuery} query
     * @returns {Promise<AxiosResponse<EntryCollection>>}
     */
    public async listFoo(query?: GetQuery): Promise<AxiosResponse<EntryCollection>> {
        let params: AxiosRequestConfig = {
            method: 'GET',
            params: query,
            responseType: 'json',
            headers: {
                Accept: 'application/json',
            },
        };

        return this.httpClient.get<EntryCollection>(this.url, params);
    }

    /**
     * @param {EntryCreate} data
     * @returns {Promise<AxiosResponse<EntryMessage>>}
     */
    public async createFoo(data?: EntryCreate): Promise<AxiosResponse<EntryMessage>> {
        let params: AxiosRequestConfig = {
            method: 'POST',
            responseType: 'json',
            headers: {
                Accept: 'application/json',
            },
        };

        return this.httpClient.post<EntryMessage>(this.url, data, params);
    }

    /**
     * @param {EntryUpdate} data
     * @returns {Promise<AxiosResponse<EntryMessage>>}
     */
    public async put(data?: EntryUpdate): Promise<AxiosResponse<EntryMessage>> {
        let params: AxiosRequestConfig = {
            method: 'PUT',
            responseType: 'json',
            headers: {
                Accept: 'application/json',
            },
        };

        return this.httpClient.put<EntryMessage>(this.url, data, params);
    }

    /**
     * @returns {Promise<AxiosResponse<EntryMessage>>}
     */
    public async delete(): Promise<AxiosResponse<EntryMessage>> {
        let params: AxiosRequestConfig = {
            method: 'DELETE',
            responseType: 'json',
            headers: {
                Accept: 'application/json',
            },
        };

        return this.httpClient.delete(this.url, params);
    }

    /**
     * @param {EntryPatch} data
     * @returns {Promise<AxiosResponse<EntryMessage>>}
     */
    public async patch(data?: EntryPatch): Promise<AxiosResponse<EntryMessage>> {
        let params: AxiosRequestConfig = {
            method: 'PATCH',
            responseType: 'json',
            headers: {
                Accept: 'application/json',
            },
        };

        return this.httpClient.patch<EntryMessage>(this.url, data, params);
    }

}
