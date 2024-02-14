const client = new Client()
client.get(name: string, type: string, startIndex: number, float: number, boolean: boolean, date: string, datetime: string): EntryCollection
client.create(name: string, type: string, payload: EntryCreate): EntryMessage throws EntryMessage
client.update(name: string, type: string, payload: Record<string, EntryUpdate>): Record<string, EntryMessage> throws EntryMessage, Record<string, EntryMessage>
client.delete(name: string, type: string): void
client.patch(name: string, type: string, payload: Array<EntryPatch>): Array<EntryMessage> throws EntryMessage, Array<EntryMessage>


interface EntryCollection {
    entry?: Array<Entry>
}

interface Entry {
    id?: number
    userId?: number
    title?: string
    date?: string
}

interface EntryMessage {
    success?: boolean
    message?: string
}

interface EntryCreate {
    id?: number
    userId?: number
    title: string
    date: string
}

interface EntryUpdate {
    id: number
    userId?: number
    title?: string
    date?: string
}

interface EntryDelete {
    id: number
    userId?: number
    title?: string
    date?: string
}

interface EntryPatch {
    id: number
    userId?: number
    title?: string
    date?: string
}
