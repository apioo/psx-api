/**
 * BarByYearResource generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import {AxiosInstance, AxiosRequestConfig, AxiosResponse} from "axios";
import {ResourceAbstract} from "sdkgen-client"
import {EntryCollection} from "./EntryCollection";
import {EntryCreate} from "./EntryCreate";
import {EntryMessage} from "./EntryMessage";

export default class BarByYearResource extends ResourceAbstract {
    private url: string;

    private year: string;

    public constructor(year: string, baseUrl: string, httpClient: AxiosInstance) {
        super(baseUrl, httpClient);

        this.year = year;

        this.url = baseUrl + "/bar/"+year+"";
    }

    /**
     * Returns a collection
     *
     * @returns {Promise<AxiosResponse<EntryCollection>>}
     */
    public async get(): Promise<AxiosResponse<EntryCollection>> {
        let params: AxiosRequestConfig = {
            method: 'GET',
        };

        return this.httpClient.get<EntryCollection>(this.url, params);
    }

    /**
     * @param {EntryCreate} data
     * @returns {Promise<AxiosResponse<EntryMessage>>}
     */
    public async post(data?: EntryCreate): Promise<AxiosResponse<EntryMessage>> {
        let params: AxiosRequestConfig = {
            method: 'POST',
        };

        return this.httpClient.post<EntryMessage>(this.url, data, params);
    }

}
