"use client";

import { useState } from "react";
import { api } from "@/services/api";
import { MessageInputProps } from "@/types/components";

export default function MessageInput({ conversationId }: MessageInputProps) {
  const [message, setMessage] = useState("");

  async function sendMessage() {
    if (!message.trim()) return;

    await api.post("/messages", {
      conversation_id: conversationId,
      content: message,
    });

    setMessage("");
  }

  return (
    <div className="flex border-t">
      <input
        className="flex-1 p-3 outline-none"
        value={message}
        onChange={(e) => setMessage(e.target.value)}
        placeholder="Type a message..."
        onKeyDown={(e) => {
          if (e.key === "Enter") {
            sendMessage();
          }
        }}
      />
      <button onClick={sendMessage} className="px-4">
        Send
      </button>
    </div>
  );
}
