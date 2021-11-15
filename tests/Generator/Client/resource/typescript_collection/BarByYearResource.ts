/**
 * BarByYearResource generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import Axios, {AxiosInstance, AxiosPromise, Method} from "axios";
import {EntryCollection} from "./EntryCollection";
import {EntryCreate} from "./EntryCreate";
import {EntryMessage} from "./EntryMessage";

export default class BarByYearResource extends ResourceAbstract {
    private url: string;

    private year: string;

    public constructor(year: string, baseUrl: string, httpClient?: AxiosInstance) {
        super(baseUrl, httpClient);

        this.year = year;

        this.url = baseUrl + "/bar/"+year+"";
    }

    /**
     * Returns a collection
     *
     * @returns {AxiosPromise<EntryCollection>}
     */
    public get(): AxiosPromise<EntryCollection> {
        let params = {
            method: <Method> "GET",
        };

        return this.httpClient.get<EntryCollection>(this.url, params);
    }

    /**
     * @param {EntryCreate} data
     * @returns {AxiosPromise<EntryMessage>}
     */
    public post(data?: EntryCreate): AxiosPromise<EntryMessage> {
        let params = {
            method: <Method> "POST",
        };

        return this.httpClient.post<EntryMessage>(this.url, data, params);
    }

}
