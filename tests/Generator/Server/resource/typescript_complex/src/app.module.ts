import { Module } from '@nestjs/common';
import { BarController } from './controller/foo/bar.controller';
import { BazController } from './controller/foo/baz.controller';
import { BarController } from './controller/bar.controller';

@Module({
  imports: [],
  controllers: [BarController, BazController, BarController, ],
  providers: [],
})
export class AppModule {}
