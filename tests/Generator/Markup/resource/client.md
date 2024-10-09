const client = new Client()
client.get(name: string, type: string, startIndex: number, float: number, boolean: boolean, date: string, datetime: string, args: Entry): EntryCollection
client.create(name: string, type: string, payload: EntryCreate): EntryMessage throws EntryMessage
client.update(name: string, type: string, payload: Map<string, EntryUpdate>): Map<string, EntryMessage> throws EntryMessage, Map<string, EntryMessage>
client.delete(name: string, type: string): void
client.patch(name: string, type: string, payload: Array<EntryPatch>): Array<EntryMessage> throws EntryMessage, Array<EntryMessage>


export class EntryCollection {
    entry?: Array<Entry>
}

export class Entry {
    id?: number
    userId?: number
    title?: string
    date?: string
}

export class EntryMessage {
    success?: boolean
    message?: string
}

export class EntryCreate {
    id?: number
    userId?: number
    title?: string
    date?: string
}

export class EntryUpdate {
    id?: number
    userId?: number
    title?: string
    date?: string
}

export class EntryDelete {
    id?: number
    userId?: number
    title?: string
    date?: string
}

export class EntryPatch {
    id?: number
    userId?: number
    title?: string
    date?: string
}
