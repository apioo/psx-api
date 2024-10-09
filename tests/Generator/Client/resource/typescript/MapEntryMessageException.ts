/**
 * MapEntryMessageException automatically generated by SDKgen please do not edit this file manually
 * {@link https://sdkgen.app}
 */

import {KnownStatusCodeException} from "sdkgen-client"

import {EntryMessage} from "./EntryMessage";

export class MapEntryMessageException extends KnownStatusCodeException {

    public constructor(private payload: Map<string, EntryMessage>) {
        super('The server returned an error');
    }

    public getPayload(): Map<string, EntryMessage> {
        return this.payload;
    }

}
