"use client";

import { Message } from "@/types/message";

interface Props {
  messages: Message[];
}

export default function MessageList({ messages }: Props) {
  return (
    <div className="flex flex-col gap-2 p-4">
      {messages.map((message) => (
        <div
          key={message.id}
          className="bg-gray-600 p-3 rounded-md max-w-[60%]"
        >
          {message.message}
        </div>
      ))}
    </div>
  );
}
