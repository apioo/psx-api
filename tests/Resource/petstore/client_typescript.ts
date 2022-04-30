/**
 * PetsResource generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import {AxiosInstance, AxiosRequestConfig, AxiosResponse} from "axios";
import {ResourceAbstract} from "sdkgen-client"
import {PetsGetQuery} from "./PetsGetQuery";
import {Pets} from "./Pets";
import {Pet} from "./Pet";

export default class PetsResource extends ResourceAbstract {
    private url: string;


    public constructor(baseUrl: string, httpClient: AxiosInstance) {
        super(baseUrl, httpClient);


        this.url = baseUrl + "/pets";
    }

    /**
     * List all pets
     *
     * @param {PetsGetQuery} query
     * @returns {AxiosResponse<Pets>}
     */
    public async listPets(query?: PetsGetQuery): AxiosResponse<Pets> {
        let params: AxiosRequestConfig = {
            method: 'GET',
            params: query,
            responseType: 'json',
            headers: {
                Accept: 'application/json',
            },
        };

        return this.httpClient.get<Pets>(this.url, params);
    }

    /**
     * Create a pet
     *
     * @param {Pet} data
     * @returns {AxiosResponse<void>}
     */
    public async createPets(data?: Pet) {
        let params: AxiosRequestConfig = {
            method: 'POST',
            responseType: 'json',
            headers: {
                Accept: 'application/json',
            },
        };

        return this.httpClient.post(this.url, data, params);
    }

}

/**
 * Pet generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

export interface Pet {
    id: number
    name: string
    tag?: string
}

/**
 * Pets generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import {Pet} from "./Pet";
export interface Pets {
    pets?: Array<Pet>
}

/**
 * Error generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

export interface Error {
    code: number
    message: string
}

/**
 * PetsGetQuery generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

export interface PetsGetQuery {
    limit?: number
}

/**
 * PetsPetIdGetQuery generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

export interface PetsPetIdGetQuery {
    petId?: string
}

/**
 * Client generated on 0000-00-00
 * {@link https://sdkgen.app}
 */

import {ClientAbstract, CredentialsInterface, TokenStoreInterface} from "sdkgen-client"

import PetsResource from "./PetsResource";

export default class Client extends ClientAbstract {
    public constructor(baseUrl: string, credentials: CredentialsInterface|null = null, tokenStore: TokenStoreInterface|null = null, scopes: Array<string>|null = []) {
        super(baseUrl, credentials, tokenStore, scopes);
    }

    /**
     * Endpoint: /pets
     */
    public async getPets(): Promise<PetsResource>
    {
        return new PetsResource(
            this.baseUrl,
            await this.newHttpClient()
        );
    }

}
