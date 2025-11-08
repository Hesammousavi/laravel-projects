"use client";

import React, { useState, ChangeEvent } from "react";
import { ulid } from "ulidx";

interface UploadResponse {
  done?: boolean;
  chunk_received?: number;
  status?: string;
  file?: {
    id?: number;
    name?: string;
    path?: string;
    size?: number;
    mime_type?: string;
  };
}

export default function ChunkUploader(): JSX.Element {
  const [file, setFile] = useState<File | null>(null);
  const [progress, setProgress] = useState<number>(0);
  const [uploading, setUploading] = useState<boolean>(false);
  const chunkSize = 1024 * 1024 * 0.256; // 256 KB per chunk

  const handleFileChange = (e: ChangeEvent<HTMLInputElement>) => {
    const selectedFile = e.target.files?.[0] || null;
    setFile(selectedFile);
    setProgress(0);
  };

  const uploadFile = async (): Promise<void> => {
    if (!file) return;
    setUploading(true);

    const totalChunks = Math.ceil(file.size / chunkSize);
    const fileIdentifier = `${file.name}-${ulid()}`;
    let uploadedBytes = 0;

    console.log("🧩 File Identifier:", fileIdentifier);

    for (let chunkNumber = 1; chunkNumber <= totalChunks; chunkNumber++) {
      const start = (chunkNumber - 1) * chunkSize;
      const end = Math.min(start + chunkSize, file.size);
      const chunk = file.slice(start, end);

      const formData = new FormData();
      formData.append("file", chunk);
      formData.append("chunk_number", chunkNumber.toString());
      formData.append("total_chunks", totalChunks.toString());
      formData.append("file_identifier", fileIdentifier);
      formData.append("original_name", file.name);

      try {
        const response = await fetch("http://localhost:8000/api/upload-chunk", {
          method: "POST",
          body: formData,
        });

        if (!response.ok) throw new Error(`Server error: ${response.status}`);

        const data: UploadResponse = await response.json();

        uploadedBytes += chunk.size;
        const percent = Math.round((uploadedBytes / file.size) * 100);
        setProgress(percent);
      } catch (error) {
        console.error(`❌ Upload failed at chunk ${chunkNumber}`, error);
        alert("Upload failed. Check console for details.");
        setUploading(false);
        return;
      }
    }

    setUploading(false);
    alert("✅ Upload complete!");
  };

  return (
    <div className="max-w-md mx-auto mt-10 p-6 bg-white rounded-2xl shadow-md border">
      <h2 className="text-xl font-semibold mb-4 text-gray-800">
        Chunk File Upload
      </h2>

      <input
        type="file"
        onChange={handleFileChange}
        className="block w-full mb-4 text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full
                   file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700
                   hover:file:bg-indigo-100 cursor-pointer"
      />

      {file && (
        <div className="mb-4 text-sm text-gray-500">
          <p>📄 {file.name}</p>
          <p>💾 {(file.size / (1024 * 1024)).toFixed(2)} MB</p>
        </div>
      )}

      <button
        disabled={!file || uploading}
        onClick={uploadFile}
        className={`w-full py-2 px-4 rounded-md text-white font-medium ${
          uploading
            ? "bg-gray-400 cursor-not-allowed"
            : "bg-indigo-600 hover:bg-indigo-700"
        }`}
      >
        {uploading ? "در حال آپلود..." : "شروع آپلود"}
      </button>

      <div className="mt-5 h-4 bg-gray-200 rounded-full overflow-hidden">
        <div
          className="h-full bg-indigo-500 transition-all duration-300 ease-in-out"
          style={{ width: `${progress}%` }}
        ></div>
      </div>

      <p className="text-sm text-gray-600 text-center mt-2">{progress}%</p>
    </div>
  );
}
