/**
 * PetsResource generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

import Axios, {AxiosInstance, AxiosPromise, Method} from "axios";
import {PetsGetQuery} from "./PetsGetQuery";
import {Pets} from "./Pets";
import {Pet} from "./Pet";

export default class PetsResource {
    private url: string;
    private token: string;
    private httpClient: AxiosInstance;


    public constructor(baseUrl: string, token: string, httpClient?: AxiosInstance) {

        this.url = baseUrl + "/pets";
        this.token = token;
        this.httpClient = httpClient ? httpClient : Axios.create();
    }

    /**
     * List all pets
     *
     * @param {PetsGetQuery} query
     * @returns {AxiosPromise<Pets>}
     */
    public listPets(query?: PetsGetQuery): AxiosPromise<Pets> {
        let params = {
            method: <Method> "GET",
            params: query,
        };

        return this.httpClient.get<Pets>(this.url, params);
    }

    /**
     * Create a pet
     *
     * @param {Pet} data
     * @returns {AxiosPromise<void>}
     */
    public createPets(data?: Pet) {
        let params = {
            method: <Method> "POST",
        };

        return this.httpClient.post(this.url, data, params);
    }

}

/**
 * Pet generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

export interface Pet {
    id: number
    name: string
    tag?: string
}

/**
 * Pets generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

import {Pet} from "./Pet";
export interface Pets {
    pets?: Array<Pet>
}

/**
 * Error generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

export interface Error {
    code: number
    message: string
}

/**
 * PetsGetQuery generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

export interface PetsGetQuery {
    limit?: number
}

/**
 * PetsPetIdGetQuery generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

export interface PetsPetIdGetQuery {
    petId?: string
}

/**
 * Client generated on 0000-00-00
 * {@link https://github.com/apioo}
 */

import Axios, {AxiosInstance, AxiosPromise} from "axios";
import PetsResource from "./PetsResource";

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
     * Endpoint: /pets
     * 
     * @returns PetsResource
     */
    public getPets(): PetsResource
    {
        return new PetsResource(
            this.baseUrl,
            this.token,
            this.httpClient
        );
    }

}
