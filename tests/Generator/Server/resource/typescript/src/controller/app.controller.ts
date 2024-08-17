import { Controller, Get, Post, Put, Patch, Delete, HttpCode, Param, Query, Headers, Body } from '@nestjs/common'

@Controller()
export class AppController {
  @Get()
  @HttpCode(200)
  get(@Param('name') name: string, @Param('type') type: string, @Query('startIndex') startIndex?: number, @Query('float') float?: number, @Query('boolean') boolean?: boolean, @Query('date') date?: string, @Query('datetime') datetime?: string, @Query('args') args?: Entry): EntryCollection {
    // @TODO implement method
  }

  @Post()
  @HttpCode(201)
  create(@Param('name') name: string, @Param('type') type: string, @Body() payload: EntryCreate): EntryMessage {
    // @TODO implement method
  }

  @Put()
  @HttpCode(200)
  update(@Param('name') name: string, @Param('type') type: string, @Body() payload: Record<string, EntryUpdate>): Record<string, EntryMessage> {
    // @TODO implement method
  }

  @Delete()
  @HttpCode(204)
  delete(@Param('name') name: string, @Param('type') type: string, @Body() payload: EntryDelete): EntryMessage {
    // @TODO implement method
  }

  @Patch()
  @HttpCode(200)
  patch(@Param('name') name: string, @Param('type') type: string, @Body() payload: Array<EntryPatch>): Array<EntryMessage> {
    // @TODO implement method
  }

}
