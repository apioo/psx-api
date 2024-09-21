/**
 * XmlException automatically generated by SDKgen please do not edit this file manually
 * {@link https://sdkgen.app}
 */

import {KnownStatusCodeException} from "sdkgen-client"


export class XmlException extends KnownStatusCodeException {

    public constructor(private payload: string) {
        super('The server returned an error');
    }

    public getPayload(): string {
        return this.payload;
    }

}
